# [Maven mvn install 本地jar添加到maven仓库中](https://blog.csdn.net/mingjie1212/article/details/78395744)

最近在使用支付宝、财付通这样的第三方支付，在使用支付宝过程中需要引入官方SDK方便开发，使用以下命令来将本地的jar装载到maven仓库中。


这里有几点需要注意点，我使用Windows10时，使用powershell 死活不可以，报错误：
[ERROR] The goal you specified requires a project to execute but there is no POM in this directory 无奈使用cmd 就没问题了
另外需要注意的是-Dfile的参数中不要有空格等特殊字符。




运行以下命令（前提你已经将maven加入环境变量中）
```
mvn install:install-file -DgroupId=alipay -DartifactId=alipay-trade-sdk -Dversion=1.0 -Dpackaging=jar -Dfile=F:\支付宝SDKJARlongguo\alipay-trade-sdk.jar
mvn install:install-file -DgroupId=alipay -DartifactId=alipay-sdk-java20151021120052 -Dversion=1.0 -Dpackaging=jar -Dfile=F:\支付宝SDKJARlongguo\alipay-sdk-java20151021120052.jar


build success!
```



然后使用时，在pom中添加

```
<dependency>
   <groupId>alipay</groupId>
   <artifactId>alipay-trade-sdk</artifactId>
   <version>1.0</version>
</dependency>

<dependency>
   <groupId>alipay</groupId>
   <artifactId>alipay-sdk-java20151021120052</artifactId>
   <version>1.0</version>
</dependency>
```

————————————————
版权声明：本文为CSDN博主「mingjie1212」的原创文章，遵循 CC 4.0 BY-SA 版权协议，转载请附上原文出处链接及本声明。
原文链接：https://blog.csdn.net/mingjie1212/article/details/78395744