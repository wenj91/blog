
# locate
yum install mlocate
安装完后执行updatedb就可以正常使用了

# readelf
查看库/可执行文件elf格式

# sed 
sed -i 's/\r$//' <filename> 
但是有特殊字符时则失效，需要将‘／’替换成‘#’

# killall
yum install psmisc
该命令可以快速杀死所有指定名称进程
killall -9 xx(进程名称)