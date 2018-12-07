# 列举有效包
yum list zlib-devel


# 添加swap区
function addSwap() {
    echo "添加 Swap..."
#   if read -n1 -p "请输入虚拟内存大小（正整数、单位为GB、默认6  GB）" answer
#   then
#   /bin/dd if=/dev/zero of=/var/swap.1 bs=1M count=1000*$answer
    /bin/dd if=/dev/zero of=/var/swap.1 bs=1M count=8000
    mkswap /var/swap.1
    swapon /var/swap.1
#   加入开机自动挂载
#   $ 最后一行
#   a 在该指令前面的行数后面插入该指令后面的内容
    sed -i '$a /var/swap.1 swap swap default 0 0' /etc/fstab
    echo "添加 Swap 成功"
}


# linux
[linux(centos7) 查看磁盘空间大小](https://blog.csdn.net/xudailong_blog/article/details/80850228)

df -hl 查看磁盘剩余空间  
df -h 查看每个根路径的分区大小  
du -sh [目录名] 返回该目录的大小  
du -sm [文件夹] 返回该文件夹总M数  
du -h [目录名] 查看指定文件夹下的所有文件大小（包含子文件夹）

## linux 64位系统编译32位程序
* 添加如下两条环境变量  
for gcc: `export CFLAGS=-m32`  
for g++: `export CXXFLAGS=-m32`  
* 并添加如下库:  
`sudo yum install glibc-devel.i686 libgcc.i686 libstdc++-devel.i686 ncurses-devel.i686`
