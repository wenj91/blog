# [docker部署nginx--dockerfile方法](https://my.oschina.net/u/3746745/blog/1811278)

author：@c1awn  
env如下:  
如无特殊说明，docker版本为：  

```bash
[root@c1awn01 ~]# docker version
Client:
 Version:         1.13.1
 API version:     1.26
 Package version: <unknown>
 Go version:      go1.8.3
 Git commit:      774336d/1.13.1
 Built:           Wed Mar  7 17:06:16 2018
 OS/Arch:         linux/amd64
 ```

linux版本：

```bash
[root@c1awn01 ~]# uname -a
Linux c1awn01 3.10.0-693.21.1.el7.x86_64 #1 SMP Wed Mar 7 19:03:37 UTC 2018 x86_64 x86_64 x86_64 GNU/Linux
[root@c1awn01 ~]# cat /etc/redhat-release 
CentOS Linux release 7.4.1708 (Core) 
```

## 1. 环境
centos7
yum安装 的nginx，配置和目录保持默认

```bash
rpm -Uvh http://nginx.org/packages/centos/7/noarch/RPMS/nginx-release-centos-7-0.el7.ngx.noarch.rpm

yum install -y nginx
```

## 2. 创建dockerfile

```bash
[root@c1awn01 nginx]# cat dockerfile 
FROM nginx
MAINTAINER c1awn
ENV RUN_USER nginx
ENV RUN_GROUP nginx
ENV DATA_DIR /data/web
ENV LOG_DIR /data/log/nginx
RUN mkdir /data/log/nginx -p
RUN chown nginx.nginx -R /data/log/nginx
ADD html /data/web
ADD nginx.conf /etc/nginx/nginx.conf
ADD conf.d/default.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
ENTRYPOINT nginx -g "daemon off;"
```

分段解析：

镜像来源，官方nginx  
此派生镜像维护者  
镜像用户、用户组都是nginx  
镜像data目录/data/web  
镜像log目录/data/log/nginx  
创建镜像的日志目录，赋予所有者、组  
将当前目录下的网页目录、nginx.conf 、conf.d/default.conf分别复制到镜像目录中  
指定镜像默认端口80  
ENTRYPOINT nginx -g指定开启镜像立刻执行的命令  
daemon off;是为了防止容器跑着跑着挂掉  
容器为什么加daemon off  

```bash
[root@c1awn01 nginx]# pwd
/etc/nginx
[root@c1awn01 nginx]# ll
总用量 40
drwxr-xr-x 2 root root   26 5月  12 13:59 conf.d
-rw-r--r-- 1 root root  343 5月  12 14:45 dockerfile
-rw-r--r-- 1 root root 1007 4月  17 23:48 fastcgi_params
drwxr-xr-x 2 root root   40 5月  12 14:20 html
-rw-r--r-- 1 root root 2837 4月  17 23:48 koi-utf
-rw-r--r-- 1 root root 2223 4月  17 23:48 koi-win
-rw-r--r-- 1 root root 5170 4月  17 23:48 mime.types
lrwxrwxrwx 1 root root   29 5月  12 13:51 modules -> ../../usr/lib64/nginx/modules
-rw-r--r-- 1 root root  643 4月  17 23:46 nginx.conf
-rw-r--r-- 1 root root  636 4月  17 23:48 scgi_params
-rw-r--r-- 1 root root  664 4月  17 23:48 uwsgi_params
-rw-r--r-- 1 root root 3610 4月  17 23:48 win-utf
```

当前目录是yum安装nginx的配置文件目录，为了方便，将`/usr/share/nginx/html`也复制到此目录下

## 3. docker bulid创建镜像  

```bash
[root@c1awn01 nginx]# docker build -t test_nginx .
Sending build context to Docker daemon 32.26 kB
Step 1/13 : FROM nginx
 ---> ae513a47849c
Step 2/13 : MAINTAINER c1awn
 ---> Using cache
 ---> 6865842b23dd
Step 3/13 : ENV RUN_USER nginx
 ---> Using cache
 ---> a102ce453221
Step 4/13 : ENV RUN_GROUP nginx
 ---> Using cache
 ---> 2e8340134b66
Step 5/13 : ENV DATA_DIR /data/web
 ---> Using cache
 ---> cf5ff12aa091
Step 6/13 : ENV LOG_DIR /data/log/nginx
 ---> Using cache
 ---> bf0e81cc24a8
Step 7/13 : RUN mkdir /data/log/nginx -p
 ---> Using cache
 ---> 2e781aa2ddfd
Step 8/13 : RUN chown nginx.nginx -R /data/log/nginx
 ---> Using cache
 ---> d488d4be88ce
Step 9/13 : ADD html /data/web
 ---> Using cache
 ---> 5f339b72e09a
Step 10/13 : ADD nginx.conf /etc/nginx/nginx.conf
 ---> Using cache
 ---> 6fb7afa7c879
Step 11/13 : ADD conf.d/default.conf /etc/nginx/conf.d/default.conf
 ---> Using cache
 ---> 4dadb8f9362c
Step 12/13 : EXPOSE 80
 ---> Using cache
 ---> 5496741b35be
Step 13/13 : ENTRYPOINT nginx -g "daemon off;"
 ---> Using cache
 ---> 7d17550c0281
Successfully built 7d17550c0281
```

注意docker build最后有个点
可以看到13个创建的步骤

## 4.后台启动镜像，常见启动方式  
上一步的Successfully built 7d17550c0281显示了创建的镜像ID

```bash
[root@c1awn01 nginx]# docker run -d --name nginx281 -p 127.0.0.1:8080:80 7d17550c0281
c2af6abe0b22f07a74ea279d75a44635282db2053a0ea70c1d8dabb9e3427619
```

-d 后台运行  
--name 指定name  
-p 宿主机ip：端口：镜像端口  

**curl测试一下镜像的网页**

```bash
[root@c1awn01 nginx]# curl 127.0.0.1:8080
<!DOCTYPE html>
<html>
<head>
<title>Welcome to nginx!</title>
<style>
   body {
       width: 35em;
       margin: 0 auto;
       font-family: Tahoma, Verdana, Arial, sans-serif;
   }
</style>
</head>
<body>
<h1>Welcome to nginx!</h1>
<p>If you see this page, the nginx web server is successfully installed and
working. Further configuration is required.</p>
<p>For online documentation and support please refer to
<a href="http://nginx.org/">nginx.org</a>.<br/>
Commercial support is available at
<a href="http://nginx.com/">nginx.com</a>.</p>
<p><em>Thank you for using nginx.</em></p>
</body>
</html>
```

成功访问nginx容器的HTML文件

## 5.进入nginx容器的伪终端，查看data目录

```bash
docker exec -it c2af6abe0b22 bash 进入容器伪终端bash界面

[root@c1awn01 nginx]# docker exec -it c2af6abe0b22 bash
root@c2af6abe0b22:/# ls /data/web/
50x.html  index.html
root@c2af6abe0b22:/# ls /data/log/
nginx
root@c2af6abe0b22:/# ls /data/log/nginx/
root@c2af6abe0b22:/# 
```

## 6.问题：nginx始终返回官方index而不是自己写的index
注意`conf.d/default.conf`定义的页面目录，比如下面的重新定义为容器的`/data/web/`，而默认目录是`/usr/share/nginx/html`

```conf
server {
    listen       80;
    server_name  localhost;

    #charset koi8-r;
    #access_log  /var/log/nginx/host.access.log  main;

    location / {
        root   /data/web/;
        index  index.html index.htm;
    }
}
```

事先在当前目录下的`html/index.html`里加了`Welcome to nginx! This is a demo!` 假定这是自己写的index  
重新build镜像  
重新启动镜像 ， 现在直接把容器端口直接映射在宿主机80上  

```bash
[root@c1awn01 nginx]# curl  127.0.0.1
<!DOCTYPE html>
<html>
<head>
<title>Welcome to nginx! This is a demo!</title>
<style>
    body {
        width: 35em;
        margin: 0 auto;
        font-family: Tahoma, Verdana, Arial, sans-serif;
    }
</style>
</head>
<body>
<h1>Welcome to nginx!</h1>
<p>If you see this page, the nginx web server is successfully installed and
working. Further configuration is required.</p>

<p>For online documentation and support please refer to
<a href="http://nginx.org/">nginx.org</a>.<br/>
Commercial support is available at
<a href="http://nginx.com/">nginx.com</a>.</p>

<p><em>Thank you for using nginx.</em></p>
</body>
</html>
```

返回结果说明nginx官方镜像也解析了新的index