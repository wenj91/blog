# [4种方式配置不同作用域的jvm的堆栈内存。](https://www.cnblogs.com/lytwajue/p/7120031.html)

1、Eclise 中设置jvm内存: 改动eclipse的配置文件,对全部project都起作用

     改动eclipse根文件夹下的eclipse.ini文件

     -vmargs  //虚拟机设置

     -Xms40m //初始内存

     -Xmx256m //最大内存

     -Xmn16m //最小内存

     -XX:PermSize=128M //非堆内存

     -XX:MaxPermSize=256M

 2、Eclise 中设置jvm内存:jres VM Arguments參数的设置,对全部project都起作用

     打开eclipse  window－preferences－Java－Installed JREs －Edit－Default VM Arguments   

     在VM自变量中输入：-Xmx128m -Xms64m -Xmn32m -Xss16m

3、Eclise 中设置jvm内存:RunConfigurations  VM arguments參数设置,仅仅对这个project起作用

在Eclipse中-->右击project/要执行的主类-->Run As-->RunConfigurations-->(x)=Arguments-->VM arguments

中增加 -Xmx36m

然后Apply-->Run
这上面的36指的是给java虚拟机分配的最大堆内存大小，单位是MB，也就是说上面的那句话的含义是JVM的最大堆内存是36MB
4、Eclise 中设置jvm内存:Debug Configurations  VM arguments參数设置,仅仅对这个project起作用

在Eclipse中-->右击project/要执行的主类-->Debug As-->DebugConfigurations-->(x)=Arguments-->VM arguments

中增加-Xmx36m

然后Apply-->Run
这上面的36指的是给java虚拟机分配的最大堆内存大小。单位是MB，也就是说上面的那句话的含义是JVM的最大堆内存是36MB
 5、Tomcat内存设置

windows下在catalina.bat的第一行添加： 

Java代码 :set JAVA_OPTS=-Xms64m -Xmx256m -XX:PermSize=128M -XX:MaxNewSize=256m -XX:MaxPermSize=256m  

linux下在catalina.sh的第一行添加： 

Java代码 :JAVA_OPTS=-Xms64m -Xmx256m -XX:PermSize=128M -XX:MaxNewSize=256m -XX:MaxPermSize=256m


实例+具体解释

设置Tomcat启动的初始内存其初始空间(即-Xms)是物理内存的1/64。最大空间(-Xmx)是物理内存的1/4。

能够利用JVM提供的-Xmn -Xms -Xmx等选项可进行设置

实例，下面给出1G内存环境下java jvm 的參数设置參考：

JAVA_OPTS="-server -Xms800m -Xmx800m -XX:PermSize=64M -XX:MaxNewSize=256m -XX:MaxPermSize=128m -Djava.awt.headless=true "

JAVA_OPTS="-server -Xms768m -Xmx768m -XX:PermSize=128m -XX:MaxPermSize=256m -XX: NewSize=192m -XX:MaxNewSize=384m"

CATALINA_OPTS="-server -Xms768m -Xmx768m -XX:PermSize=128m -XX:MaxPermSize=256m -XX:NewSize=192m -XX:MaxNewSize=384m"

Linux：

在/usr/local/apache-tomcat-5.5.23/bin 文件夹下的catalina.sh加入：

JAVA_OPTS='-Xms512m -Xmx1024m'要加“m”说明是MB。否则就是KB了，在启动tomcat时会 报内存不足。

-Xms：初始值-Xmx：最大值-Xmn：最小值

Windows：

在catalina.bat最前面增加set JAVA_OPTS=-Xms128m -Xmx350m 

假设用startup.bat启动tomcat,OK设置生效.够成功的分配200M内存.

可是假设不是运行startup.bat启动tomcat而是利用windows的系统服务启动tomcat服务,上面的设置就不生效了,就是说set JAVA_OPTS=-Xms128m -Xmx350m 没起作用.上面分配200M内存就OOM了..

windows服务运行的是bin\tomcat.exe.他读取注冊表中的值,而不是catalina.bat的设置.

解决的方法:

改动注冊表HKEY_LOCAL_MACHINE\SOFTWARE\Apache Software Foundation\Tomcat Service Manager\Tomcat5\Parameters\JavaOptions

原值为-Dcatalina.home="C:\ApacheGroup\Tomcat 5.0"-Djava.endorsed.dirs="C:\ApacheGroup\Tomcat 5.0\common\endorsed"-Xrs增加 -Xms300m -Xmx350m 

重起tomcat服务,设置生效




Tomcat 的JVM 内存溢出问题的解决keyword: tomcat 的jvm 内存溢出问题的解决 



近期在熟悉一个开发了有几年的项目，须要把数据库从mysql移植到oracle。首先把jdbc的连接指向mysql。打包放到tomcat里面，能够跑起来，没有问题，但是当把jdbc连接指向oracle的时候，tomcat就连续抛java.lang.OutOfMemoryError的错误，上网google了一下。了解了一下tomcat的执行机制，也攻克了问题。share出来，以备查。 

1、首先是：java.lang.OutOfMemoryError: Java heap space 解释：

Heap size 设置 JVM堆的设置是指java程序执行过程中JVM能够调配使用的内存空间的设置.JVM在启动的时候会自己主动设置Heap size的值。其初始空间(即-Xms)是物理内存的1/64，最大空间(-Xmx)是物理内存的1/4。能够利用JVM提供的-Xmn -Xms -Xmx等选项可进行设置。Heap size 的大小是Young Generation 和Tenured Generaion 之和。

 

提示：在JVM中假设98％的时间是用于GC且可用的Heap size 不足2％的时候将抛出此异常信息。 

提示：Heap Size 最大不要超过可用物理内存的80％，一般的要将-Xms和-Xmx选项设置为同样，而-Xmn为1/4的-Xmx值。 

解决方法：

手动设置Heap size 改动TOMCAT_HOME/bin/catalina.bat，在“echo "Using CATALINA_BASE: $CATALINA_BASE"”上面增加下面行：

Java代码 set JAVA_OPTS=%JAVA_OPTS% -server -Xms800m -Xmx800m -XX:MaxNewSize=256m

或改动catalina.sh 在“echo "Using CATALINA_BASE: $CATALINA_BASE"”上面增加下面行： JAVA_OPTS="$JAVA_OPTS -server -Xms800m -Xmx800m -XX:MaxNewSize=256m" 

2、其次是：java.lang.OutOfMemoryError: PermGen space 原因：

PermGen space的全称是Permanent Generation space,是指内存的永久保存区域。这块内存主要是被JVM存放Class和Meta信息的,Class在被Loader时就会被放到PermGen space中，它和存放类实例(Instance)的Heap区域不同,GC(Garbage Collection)不会在主程序执行期对PermGen space进行清理，所以假设你的应用中有非常CLASS的话,就非常可能出现PermGen space错误。这样的错误常见在webserver对JSP进行pre compile的时候。假设你的WEB APP下都用了大量的第三方jar, 其大小超过了jvm默认的大小(4M)那么就会产生此错误信息了。 

解决方法：

1. 手动设置MaxPermSize大小 改动TOMCAT_HOME/bin/catalina.bat（Linux下为catalina.sh），在Java代码 “echo "Using CATALINA_BASE: $CATALINA_BASE"”上面增加下面行： set JAVA_OPTS=%JAVA_OPTS% -server -XX:PermSize=128M -XX:MaxPermSize=512m

catalina.sh下为： Java代码 JAVA_OPTS="$JAVA_OPTS -server -XX:PermSize=128M -XX:MaxPermSize=512m"