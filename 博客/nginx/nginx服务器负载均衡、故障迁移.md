# [NGINX高级应用——服务器负载均衡、故障迁移](https://blog.csdn.net/weixin_42867325/article/details/83050865)

## 基于Nginx实现负载均衡
1. 轮询策略
特点:根据配置文件的顺序,依次访问不同的tomcat服务器.
配置:

配置tomcat负载均衡  1.轮询的方式
```
	upstream jt {
		server localhost:8080;
		server localhost:8081;
		server localhost:8082;
	}

	# 后台管理系统
	server {
		listen		80;
		server_name  manage.jt.com;
		location / {
			proxy_pass http://jt;
		}
	}
```
这种 nignix 反向代理服务器的配置方式、是根据我们upstream中配置的服务器的数量，将我们的请求依次循环发送给每一个服务器。
使用场景：所有的服务器规格相同，物理配置相同。‘

2. 权重
但是很多时候我们的服务器价格不同、也有主从之分，能者多劳、那么我们就应该让主服务更多的去分担服务器的压力，所以有了这种占权重比的配置方式。

配置tomcat负载均衡  1.轮询  2.权重
```
	upstream jt {
		server localhost:8080 weight=6;
		server localhost:8081 weight=3;
		server localhost:8082 weight=1;
	}
	#后台管理系统
	server {
		listen		80;
		server_name  manage.jt.com;
		location / {
			proxy_pass http://jt;
		}
	}
```
使用场景：服务器有主从之分。

3. IP_HASH
session和cookie工作方式：
当我们采用权重的配置方式，我们认为已经较为合理，但是不可避免的我们忽略了一个大问题、如果一个用户第一次来访问我们的服务器，保存了登录信息，这时候他刷新了一下网页，重新发起请求，那么这个请求就很有可能被分配到其他服务器，我们知道在保存用户登录信息的时候，我们是将用户信息保存在session里，session保存在我们的服务器端，只是将一个session的标记id交给浏览器，浏览器将其保存在cookie里，所以下次同一个用户继续发起请求的时候，cookie里面会携带浏览器加粗样式所有的cookieid所以我们可以识别用户已经登录。那么现在的情况是用户的请求被发送到不同的物理服务器里，这个session显然不能共享，那么我们怎么解决呢？

实际问题:
采用集群的方式不能实现用户Session共享.因为不同的tomcat之间是物理隔离.

解决方案:

A:采用SessionId进行URL重写.
优点:可以实现Session共享,Cookie禁用
缺点:效率太低

B:使用Nginx中IP_HASH技术.能够根据用户的IP动态的绑定到一台服务器中. 变向的实现Session共享.IP_HASH中优先级最高.配置后轮询和权重不生效.

但是这种方式也是用很大问题的，最明显的两个就是
1.如果服务器宕机,用户访问受限
2.使用IP_hash导致负载不均.

## 三、Nginx故障迁移
1. 手动下线  
`server localhost:8080 weight=6 down;`
2. 备用机机制  
`server localhost:8082 weight=1 backup;`
3. 设定超时时间  
```
proxy_connect_timeout 3;
proxy_read_timeout 3;
proxy_send_timeout 3;
```
4.健康检测
说明:在规定的周期内,用户会通过健康检测.检查当前的服务器是否可用,如果发现服务器宕机.则在当前的周期内不会再将请求发往故障机.
直到下个周期.如果当故障机修复.可以在下一个周期后继续提供服务.  

`server localhost:8080 max_fails=1 fail_timeout=60s;`

代码测试：

配置tomcat负载均衡  1.轮询  2.权重  3.ip_hash方式
```
	upstream jt {
		#ip_hash;		
		server localhost:8080 max_fails=1 fail_timeout=60s;
		server localhost:8081 max_fails=1 fail_timeout=60s;
		server localhost:8082 max_fails=1 fail_timeout=60s;
	}

	#后台管理系统
	server {
		listen		80;
		server_name  manage.jt.com;

		location / {
			proxy_pass http://jt;
			proxy_connect_timeout       3;  
			proxy_read_timeout          3;  
			proxy_send_timeout          3; 
		}
	}
```
--------------------- 
作者：Cxx、 
来源：CSDN 
原文：https://blog.csdn.net/weixin_42867325/article/details/83050865 
版权声明：本文为博主原创文章，转载请附上博文链接！