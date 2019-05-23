下载源码
网上搜的流程下载源码都是需要翻墙的,国内镜像安装如下:
  wget https://www.golangtc.com/static/go/1.9.2/go1.9.2.linux-amd64.tar.gz



解码源码
将源码解压在/usr/bin目录下面
  tar zxf go1.9.2.linux-amd64.tar.gz -C /usr/bin



设置环境变量
将go的bin目录加入环境变量
  echo "export PATH=/usr/bin/go/bin:$PATH" >> /etc/profile



环境变量即时生效
重启可以使设置的环境变量生效,除此之外还可以使之直接生效:
  source /etc/profile



查看是否安装成功
查看安装的go版本是否为1.9.2
  go version

作者：sunix
链接：https://www.jianshu.com/p/149388adaa5e
来源：简书
简书著作权归作者所有，任何形式的转载都请联系作者获得授权并注明出处。