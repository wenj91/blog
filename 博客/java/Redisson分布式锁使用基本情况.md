基于sentinel(哨兵)模式,部署了两台sentinel
用户访问量最高峰估计3000左右
RLock

最常遇到问题:
org.redisson.client.RedisTimeoutException
	at org.redisson.CommandExecutorService.async(CommandExecutorService.java:441)
	at org.redisson.CommandExecutorService$5.run(CommandExecutorService.java:437)
	at io.netty.util.HashedWheelTimer$HashedWheelTimeout.expire(HashedWheelTimer.java:581)
	at io.netty.util.HashedWheelTimer$HashedWheelBucket.expireTimeouts(HashedWheelTimer.java:655)
