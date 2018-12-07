进入docker容器后如果退出容器，容器就会变成Exited的状态，那么如何退出容器让容器不关闭呢？
如果要正常退出不关闭容器，请按`Ctrl+P+Q`进行退出容器，这一点很重要，请牢记！
以下示例为退出容器但不关闭容器
```bash
[root@localhost ~]# docker attach c600c4519fc8
[root@c600c4519fc8 /]# exit
exit
[root@localhost ~]# docker ps -a
CONTAINER ID        IMAGE               COMMAND             CREATED             STATUS                    PORTS               NAMES
c600c4519fc8        centos              "/bin/bash"         3 hours ago         Exited (0) 1 second ago                       pensive_jackson
5a7a0d694651        busybox             "sh"                20 hours ago        Exited (0) 20 hours ago                       hungry_vaughan
4b0296d18849        hello-world         "/hello"            46 hours ago        Exited (0) 46 hours ago                       hopeful_yonath
[root@localhost ~]# docker start pensive_jackson
pensive_jackson
[root@localhost ~]# docker attach c600c4519fc8
```
## Ctrl + P + Q 
```bash
[root@c600c4519fc8 /]# read escape sequence
[root@localhost ~]# docker ps -a
CONTAINER ID        IMAGE               COMMAND             CREATED             STATUS                    PORTS               NAMES
c600c4519fc8        centos              "/bin/bash"         3 hours ago         Up 22 seconds                                 pensive_jackson
5a7a0d694651        busybox             "sh"                20 hours ago        Exited (0) 20 hours ago                       hungry_vaughan
4b0296d18849        hello-world         "/hello"            46 hours ago        Exited (0) 46 hours ago                       hopeful_yonath
```
事实上我们可以在启动容器的时候就进行配置，加入-d参数来启动容器，当然，这条命令仅限于启动全新的容器，启动关闭的容器是不可以的。

Tips 1  
docker run -d: 后台运行容器，并返回容器ID

以下示例为使用docker -d启动容器并退出
```bash
[root@localhost ~]# docker run -i -t -d centos /bin/bash
8521b11d5d99535d4cb0080adc5a58a4dd018ecd0751d9945f7da7ab01bec330
[root@localhost ~]# docker ps -a
CONTAINER ID        IMAGE               COMMAND             CREATED             STATUS                      PORTS               NAMES
8521b11d5d99        centos              "/bin/bash"         4 seconds ago       Up 4 seconds                                    eager_goldwasser
c600c4519fc8        centos              "/bin/bash"         3 hours ago         Exited (0) 28 seconds ago                       pensive_jackson
5a7a0d694651        busybox             "sh"                20 hours ago        Exited (0) 20 hours ago                         hungry_vaughan
4b0296d18849        hello-world         "/hello"            46 hours ago        Exited (0) 46 hours ago                         hopeful_yonath
[root@localhost ~]# docker attach 8
[root@8521b11d5d99 /]# uname -r
3.10.0-514.el7.x86_64
[root@8521b11d5d99 /]# exit
exit
[root@localhost ~]# docker ps -a
CONTAINER ID        IMAGE               COMMAND             CREATED             STATUS                     PORTS               NAMES
8521b11d5d99        centos              "/bin/bash"         2 minutes ago       Exited (0) 2 seconds ago                       eager_goldwasser
c600c4519fc8        centos              "/bin/bash"         3 hours ago         Exited (0) 2 minutes ago                       pensive_jackson
5a7a0d694651        busybox             "sh"                20 hours ago        Exited (0) 20 hours ago                        hungry_vaughan
4b0296d18849        hello-world         "/hello"            46 hours ago        Exited (0) 46 hours ago                        hopeful_yonath
```
在这里你可能会发现，使用了-d的命令退出后容器依然还是死了，动手型的朋友可能会发现只是用docker run -d去启动容器也一样是死的
这里其实需要了解的是容器的运行机制，Docker容器在后台运行，必须要有一个前台进程，这里我们让容器有前台程序运行，就可以实现容器的-d 启动后存活
```bash
[root@localhost ~]# docker ps -a
CONTAINER ID        IMAGE               COMMAND             CREATED             STATUS                     PORTS               NAMES
c600c4519fc8        centos              "/bin/bash"         3 hours ago         Exited (0) 4 minutes ago                       pensive_jackson
5a7a0d694651        busybox             "sh"                21 hours ago        Exited (0) 21 hours ago                        hungry_vaughan
4b0296d18849        hello-world         "/hello"            47 hours ago        Exited (0) 47 hours ago                        hopeful_yonath
[root@localhost ~]# docker run -d centos /bin/bash -c "nohup ping -i 1000 www.baidu.com"
8aa19c9604382bc019797ccda831ae1bcebd81d86380b6040d636e03000b440a
[root@localhost ~]# docker ps -a
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS                     PORTS               NAMES
8aa19c960438        centos              "/bin/bash -c 'nohup…"   2 seconds ago       Up 2 seconds                                   adoring_wing
c600c4519fc8        centos              "/bin/bash"              3 hours ago         Exited (0) 5 minutes ago                       pensive_jackson
5a7a0d694651        busybox             "sh"                     21 hours ago        Exited (0) 21 hours ago                        hungry_vaughan
4b0296d18849        hello-world         "/hello"                 47 hours ago        Exited (0) 47 hours ago                        hopeful_yonath
```
我这里使用nohup在后台运行一个每1000秒ping一次百度的进程，另外你也可以使用"while true; do echo hello world; sleep 1; done"，无限输出hello world。
另外即便是有进程在后台运行，你进入了容器，输入exit退出，依然会终止容器的运行，请谨记。
Ctrl+P+Q依然是我认为的最佳用法。

作者：辉耀辉耀
链接：https://www.jianshu.com/p/b1ce248d2a42
來源：简书
简书著作权归作者所有，任何形式的转载都请联系作者获得授权并注明出处。