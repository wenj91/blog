# RedissonLock核心分析

---

## 样例分析
在分析RedissonLock前首先得大致了解下redis的lua脚本,以及执行lua脚本的方式,因为RedissonLock的核心代码就是lua脚本代码

lua脚本样例:

>  
```c
local times = redis.call('incr',KEYS[1])
if times == 1 then
    redis.call('expire',KEYS[1], ARGV[1])
end
if times > tonumber(ARGV[2]) then
    return 0
end
return 1
```

> 执行:
* redis-cli --eval ratelimiting.lua rate.limitingl:127.0.0.1 , 10 3
* 分析解释:
* --eval参数是告诉redis-cli读取并运行后面的Lua脚本，ratelimiting.lua是脚本的位置，后面跟着是传给Lua脚本的参数。其中","前的rate.limiting:127.0.0.1是要操作的键，可以再脚本中用KEYS[1]获取，","后面的10和3是参数，在脚本中能够使用ARGV[1]和ARGV[2]获得。注：","两边的空格不能省略，否则会出错
* 结合脚本的内容可知这行命令的作用是将访问频率限制为每10秒最多3次，所以在终端中不断的运行此命令会发现当访问频率在10秒内小于或等于3次时返回1，否则返回0。

---

## RedissonLock分析

RLock是基于Redisson的一个同步锁实现

下面是基本使用方法:
```java
RLock rLock = redisson.getLock("lock");
rLock.lock(100, TimeUnit.SECONDS);
//Todo: your code!
rLock.unlock();
```
> 说明:
* RLock的用法其实是跟ReentrantLock使用方法很是相似
* 不同点在于RLock有一个key标识
* 还可以设置RLock存活时间(自动释放锁的时间,这点和ReentrantLock不同,ReentrantLock必须手动释放锁)
* 同时RLock也可以手动释放锁
* 以上只是从用法表面层次来看是和ReentrantLock差不多,其实本质上相差还是很大的
* 如果都一样那为何多此一举使用RLock...
* 这里说明下,最大区别是RLock利用Redis的特性实现了分布式锁机制,这个是ReentrantLock没有的特性

获得rLock之后开始对关键代码上锁,以下为Redisson执行lock的过程
```java
@Override
public void lockInterruptibly(long leaseTime, TimeUnit unit) throws InterruptedException {
    Long ttl = tryAcquire(leaseTime, unit);//-->最终指向tryLockInnerAsync,关键代码在下文会给出
    // lock acquired
    if (ttl == null) {//如果lua脚本返回结果为空说明上锁成功,否则进入循环等待中
        return;
    }

    long threadId = Thread.currentThread().getId();
    Future<RedissonLockEntry> future = subscribe(threadId);
    get(future);

    try {
        while (true) {
            ttl = tryAcquire(leaseTime, unit);
            // lock acquired
            if (ttl == null) {
                break;
            }

            // waiting for message
            if (ttl >= 0) {
                getEntry(threadId).getLatch().tryAcquire(ttl, TimeUnit.MILLISECONDS);
            } else {
                getEntry(threadId).getLatch().acquire();
            }
        }
    } finally {
        unsubscribe(future, threadId);
    }
}
```

---
#### tryLockInnerAsync:
> 上锁（异步）  
--一个key,两个参数  
 keys="lock"    
 args="30000 3a4e8d51-f191-40a4-8220-f80d39759bc0:1"  
 {      
   keys:  
		lock-->RLock存在标志  
	 args:  
		30000-->RLock存活时间,单位ms  
		3a4e8d51-f191-40a4-8220-f80d39759bc0:1-->RLock同一执行线程唯一标志，主要针对同一执行线程  
}  
```c
if (redis.call('exists', KEYS[1]) == 0) then --如果RLock不存在
  redis.call('hset', KEYS[1], ARGV[2], 1);   --则调用hset添加一个新建一个RLock
  redis.call('pexpire', KEYS[1], ARGV[1]);   --设置RLock存活时间,pexpire与expire类似,不过pexpire的时间单位是ms,而expire是s
  return nil;  								               --返回RLock
end;  
if (redis.call('hexists', KEYS[1], ARGV[2]) == 1) then  --如果同一执行线程的RLock存在，复用此锁，同时RLock的值递增1，重置RLock存活时间
  redis.call('hincrby', KEYS[1], ARGV[2], 1);  			    --RLock值递增1
  redis.call('pexpire', KEYS[1], ARGV[1]);              --设置RLock存活时间
  return nil;                                           --返回RLock
end;  
return redis.call('pttl', KEYS[1]);  --否则返回RLock剩余存活时间
```

---
#### unlock:
> 解锁  
--两个key,三个参数  
 keys="lock redisson_lock__channel__{lock}"  
 args="0 30000 3a4e8d51-f191-40a4-8220-f80d39759bc0:1"  
 {  
  keys:  
		key-->Rlock标志  
		redisson_lock__channel__{lock}-->将信息 message 发送到指定的频道 channel(PUBLISH channel message, 其他信息具体查看publish指令)  
	args:  
		0-->  
		30000-->RLock存活时间,单位ms  
		3a4e8d51-f191-40a4-8220-f80d39759bc0:1-->同一执行线程RLock标志  
}
```c
if (redis.call('exists', KEYS[1]) == 0) then --RLock不存在,锁已经释放
	redis.call('publish', KEYS[2], ARGV[1]);   --发布信息0通知到指定的频道 channel
	return 1;                                  --返回1
end;
if (redis.call('hexists', KEYS[1], ARGV[3]) == 0) then --RLock存在，但同一执行线程标志不存在
	return nil;                                          --返回nil redisson抛出异常:"attempt to unlock lock, not locked by current thread by node id: "+ id + " thread-id: " + Thread.currentThread().getId()
end;  
local counter = redis.call('hincrby', KEYS[1], ARGV[3], -1);  --RLock存在，同一执行线程标志存在，线程锁计数减1
if (counter > 0) then                                         --同一执行线程标志计数大于0
	redis.call('pexpire', KEYS[1], ARGV[2]);                    --重置线程锁存活时间
	return 0;                                                   --返回0
else                                                          --否则
	redis.call('del', KEYS[1]);                                 --删除RLock锁
	redis.call('publish', KEYS[2], ARGV[1]);                    --发布信息0通知到指定的频道 channel
	return 1;                                                   --返回1
end;  
return nil;                                                   --返回nil redisson抛出异常:"attempt to unlock lock, not locked by current thread by node id: "+ id + " thread-id: " + Thread.currentThread().getId()
```

---
#### forceUnlock:
> 强制解锁
* --两个key，一个参数
* keys="lock redisson_lock__channel__{lock}"
* args="0"
* {
  keys:
    lock-->Rlock标志
    redisson_lock__channel__{lock}-->将信息 message 发送到指定的频道 channel(PUBLISH channel message, 其他信息具体查看publish指令)
  args:
    0
}
```c
if (redis.call('del', KEYS[1]) == 1) then       --删除成功
	 redis.call('publish', KEYS[2], ARGV[1]);     --发送信息0通知到指定的频道channel
	 return 1                                     --返回1
else
	 return 0                                     --返回0
end
```

---
#### unlockAsync:
> 解锁（异步） 注解请看unlock
* --两个key，三个参数
* keys=""
* args=""
```c
if (redis.call('exists', KEYS[1]) == 0) then  
	redis.call('publish', KEYS[2], ARGV[1]);  
	return 1;  
end;
if (redis.call('hexists', KEYS[1], ARGV[3]) == 0) then  
	return nil;
end;  
local counter = redis.call('hincrby', KEYS[1], ARGV[3], -1);  
if (counter > 0) then  
	redis.call('pexpire', KEYS[1], ARGV[2]);  
	return 0;  
else  
	redis.call('del', KEYS[1]);  
	redis.call('publish', KEYS[2], ARGV[1]);  
	return 1;
end;  
return nil;
```
