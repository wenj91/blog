

## Unrecognized VM option 'UseParNewGC' , Error: Could not create the Java Virtual Machine
这个问题是由于jdk版本引起的,UseParNewGC参数在jdk1.9被标识为废弃, 在jdk10中被移除
所以编辑./config/jvm.options
将GC配置改为如下:
```conf
## GC configuration
# -XX:+UseParNewGC 
-XX:+UseG1GC # 垃圾回收器改为G1
# -XX:+UseConcMarkSweepGC # Option UseConcMarkSweepGC was deprecated in version 9
-XX:CMSInitiatingOccupancyFraction=75
-XX:+UseCMSInitiatingOccupancyOnly
```
