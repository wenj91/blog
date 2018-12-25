
#
mv /etc/apt/sources.list /etc/apt/sources.list.bak
echo "deb http://mirrors.163.com/debian/ jessie main non-free contrib" >> /etc/apt/sources.list
echo "deb http://mirrors.163.com/debian/ jessie-proposed-updates main non-free contrib" >>/etc/apt/sources.list
echo "deb-src http://mirrors.163.com/debian/ jessie main non-free contrib" >>/etc/apt/sources.list
echo "deb-src http://mirrors.163.com/debian/ jessie-proposed-updates main non-free contrib" >>/etc/apt/sources.list
#更新安装源
apt-get update 


FAQ:
W: GPG error: http://extras.ubuntu.com trusty Release: The following signatures couldn't be verified because the public key is not available: NO_PUBKEY 16126D3A3E5C1192

正确的解决方法
 sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 16126D3A3E5C1192

--------------------- 
作者：caiyj374 
来源：CSDN 
原文：https://blog.csdn.net/u014221090/article/details/77524682 
版权声明：本文为博主原创文章，转载请附上博文链接！