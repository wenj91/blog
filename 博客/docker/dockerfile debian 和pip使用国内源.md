# [dockerfile debian 和pip使用国内源](https://www.cnblogs.com/xuanmanstein/p/10071374.html)
python官方镜像是基于debian的。国内使用时定制一下，加快下载速度。

1 debian本身使用国内源
dockfile中:

#国内debian源
ADD sources.list /etc/apt/
sources.list在dockerfile同目录下：
deb http://mirrors.ustc.edu.cn/debian/ stretch main non-free contrib
deb http://mirrors.ustc.edu.cn/debian/ stretch-updates main non-free contrib
deb http://mirrors.ustc.edu.cn/debian/ stretch-backports main non-free contrib
deb-src http://mirrors.ustc.edu.cn/debian/ stretch main non-free contrib
deb-src http://mirrors.ustc.edu.cn/debian/ stretch-updates main non-free contrib
deb-src http://mirrors.ustc.edu.cn/debian/ stretch-backports main non-free contrib
deb http://mirrors.ustc.edu.cn/debian-security/ stretch/updates main non-free contrib
deb-src http://mirrors.ustc.edu.cn/debian-security/ stretch/updates main non-free contrib

2 pip使用国内源
#用国内源加速大包的安装
COPY pip.conf /etc/pip.conf
pip.conf

[global]
index-url = https://pypi.tuna.tsinghua.edu.cn/simple 
 

 

—————————————