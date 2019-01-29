
# locate
yum install mlocate
安装完后linux执行updatedb就可以正常使用了  
macos执行`/usr/libexec/locate.updatedb`

# readelf
查看库/可执行文件elf格式

# sed 
sed -i 's/\r$//' <filename> 
但是有特殊字符时则失效，需要将‘／’替换成‘#’

# killall
yum install psmisc
该命令可以快速杀死所有指定名称进程
killall -9 xx(进程名称)

# netstat
yum install net-tools

# rz sz
yum install lrzsz

# scp
复制本机数据到指定服务器指定目录  
scp /Users/wenj91/project/agent/target/goAgent.tar.gz root@web1:/usr/xerver  
复制服务器数据到本机  
scp root@web1:/usr/xerver /Users/wenj91/xxx  
