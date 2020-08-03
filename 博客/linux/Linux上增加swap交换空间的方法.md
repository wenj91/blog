# [Linux上增加swap交换空间的方法](https://www.linuxidc.com/Linux/2019-07/159395.htm)

Linux上增加swap交换空间的方法
===================

\[日期：2019-07-16\]

来源：Linux社区  作者：Linux

\[字体：[大](#) [中](#) [小](#)\]

Linux上增加交换空间有两种方法：

严格的说，在Linux系统安装完后只有一种方法可以增加swap，那就是本文的第二种方法，至于第一种方法应该是安装系统时设置交换区。

1、使用分区：

      在安装OS时划分出专门的交换分区，空间大小要事先规划好，启动系统时自动进行mount。  
      这种方法只能在安装OS时设定，一旦设定好不容易改变，除非重装系统。

2、使用swapfile：（或者是整个空闲分区）

      新建临时swapfile或者是空闲分区，在需要的时候设定为交换空间，最多可以增加8个swapfile。  
      交换空间的大小，与CPU密切相关，在i386系中，最多可以使用2GB的空间。  
      在系统启动后根据需要在2G的总容量下进行增减。  
      这种方法比较灵活，也比较方便，缺点是启动系统后需要手工设置。

下面是运用swapfile增加交换空间的步骤：

涉及到的命令：

free ---查看内存状态命令，可以显示memory，swap，buffer cache等的大小及使用状况；  
dd ---读取，转换并输出数据命令；  
mkswap ---设置交换区  
swapon ---启用交换区，相当于mount  
swapoff ---关闭交换区，相当于umount

步骤：

1、创建swapfile文件：

 root权限下，创建swapfile，假设当前目录为"/",执行如下命令：

\[root@www.linuxidc.com~\]# dd if=/dev/zero of=/swapfile bs=1G count=5  
dd: 写入"/swapfile" 出错: 设备上没有空间  
记录了5+0 的读入  
记录了4+0 的写出  
5137985536字节(5.1 GB)已复制，13.9181 秒，369 MB/秒

则在根目录下创建了一个swapfile,名称为“swapfile”，大小为5G，也可以把文件输出到自己想要的任何目录中，

个人觉得还是直接放在根目录下比较好，一目了然，不容易误破坏，放在其他目录下则不然了（当然要根目录磁盘空间要够哦！！！）；

2、将swapfile设置为swap空间

\# mkswap /swapfile  
正在设置交换空间版本 1，大小 = 5017560 KiB  
无标签，UUID=944dc5b9-7526-4fca-90d5-394aecd396bd

3、启用交换空间，这个操作有点类似于mount操作（个人理解）：

\# swapon /swapfile  
swapon: /swapfile：不安全的权限 0644，建议使用 0600。（虽有这有提示但已启用成功了，以后要注意尽量先修改文件权限为0600）

至此增加交换空间的操作结束了，可以使用free命令查看swap空间大小是否发生变化；

注：swap空间增加的话可能要目录的磁盘空盘要足够

Linux公社的RSS地址：[https://www.linuxidc.com/rssFeed.aspx](https://www.linuxidc.com/Linux/rssFeed.aspx)

**本文永久更新链接地址**：[https://www.linuxidc.com/Linux/2019-07/159395.htm](https://www.linuxidc.com/Linux/Linux/2019-07/159395.htm)

[![linux](https://www.linuxidc.com/linuxfile/logo.gif)](http://www.linuxidc.com)

[

](http://www.linuxidc.com)