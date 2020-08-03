# [Macos 安装md5sum、sha1sum、md5deep、sha1deep](https://blog.csdn.net/cup_chenyubo/article/details/52982986)

一、安装md5sum和sha1sum

方法一：brew 安装

        # brew install md5sha1sum

  

  

方法二：编译安装  

源码下载地址：[http://www.microbrew.org/tools/md5sha1sum/md5sha1sum-0.9.5.tar.gz](http://www.microbrew.org/tools/md5sha1sum/md5sha1sum-0.9.5.tar.gz)

        # tar xvfz md5sha1sum-0.9.5.tar.gz
        # cd md5sha1sum-0.9.5 
        # ./configure
        # make 
        # make install

  

二、安装md5deep和sha1deep

源码下载地址：[https://github.com/jessek/hashdeep/archive/release-4.4.tar.gz](https://github.com/jessek/hashdeep/archive/release-4.4.tar.gz)

        # tar xfvz hashdeep-release-4.4.tar.gz
        # cd hashdeep-release-4.4
        # sh bootstrap.sh
        # ./configure
        # make
        # make install

如果在执行sh bootstrap.sh时，提示aclocal不存在，则先安装automake

        # brew install automake

重新执行即可。