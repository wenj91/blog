# [Linux统计文件夹占用空间大小--du命令基本用法](https://www.cnblogs.com/justforcon/archive/2017/12/02/7954481.html)

命令行环境下要知道linux系统里一个文件夹以及其包含的文件实际所占用的空间大小，linux自带的命令 du可以很好地满足需求。

其他的用法我就不一一写出来了，就列本人觉得会用得最多的，直接上：

```bash
$ du -sh ./*
118M    ./Chemi
4.0K    ./CollectionFramework
32M    ./C程序设计 第四版 .谭浩强.扫描版pdf
7.7M    ./jsfPPT
360M    ./Mooc
34M    ./mvnt
1.4G    ./Reference
251M    ./压缩包
20K    ./面试.odt
```

输出的结果第一列是文件或者文件夹占用的体积，右侧为各文件夹

这里选项中：

-s 是计算各目录的总的空间占用，没有的话会递归列出许多没用的信息;

-h 很好理解，就是选择合适的单位，上面有的用M，有的用G，这样就一目了然了，这里du的用法就是这样了，详细的其他选项可自行参考man page。

参数里用了通配符，这样就列出所有当前目录下的文件或者文件夹，否则只列出当前所在目录的总大小。

------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

另外大家可能还会想到ls命令，一般用法是这样的：
```
$ ls -alh
total 2.0M
drwxr-xr-x 42 prompt prompt 4.0K 12月  2 13:44 .
drwxr-xr-x  6 root   root   4.0K 7月  23 16:29 ..
drwx------  3 prompt prompt 4.0K 8月  19 10:25 .adobe
drwxrwxr-x  3 prompt prompt 4.0K 7月  23 15:07 .AMD
-rw-rw-r--  1 prompt prompt  140 8月   6 10:58 .appletviewer
-rw-rw-r--  1 prompt prompt   86 8月  17 22:10 .asoundrc
-rw-------  1 prompt prompt  17K 12月  1 22:59 .bash_history
-rw-r--r--  1 prompt prompt  220 7月  23 14:16 .bash_logout
-rw-r--r--  1 prompt prompt 3.9K 8月  21 22:14 .bashrc
drwx------ 29 prompt prompt 4.0K 12月  1 15:17 .cache
drwx------  3 prompt prompt 4.0K 7月  23 15:12 .compiz
drwx------ 34 prompt prompt 4.0K 12月  1 15:17 .config
drwx------  3 prompt prompt 4.0K 7月  23 15:25 .dbus
drwxr-xr-x  2 prompt prompt 4.0K 12月  1 15:03 Desktop
-rw-r--r--  1 prompt prompt   25 7月  23 15:12 .dmrc
drwxrwxr-x  9 prompt prompt 4.0K 12月  2 14:43 Documents
drwxrwxr-x  7 prompt prompt 4.0K 12月  1 21:00 Downloads
```

这里ls的选项中大家应该都知道（-a 显示所有文件及文件夹，包括以.开头的;-l 列出详细信息，如占用空间大小，所属用户等等;-h 用合适的单位显示占用空间大小，如使用M或者G），文件的大小都是4.0K，显然算上其所含的文件是不止这么多的。

