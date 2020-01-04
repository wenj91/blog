# [nginx实现no-www和www跳转](https://www.jianshu.com/p/cec753473ec9)

## 本文将利用nginx实现以下四种跳转:

* http:no-www跳转到www
* http:www跳转到no-www
* https:no-www跳转到www
* https:www跳转到no-www

准备工作
检查一下域名解析有没有配置好，即顶级域名和www二级域名都要指向服务器ip地址，然后打开nginx配置文件nginx.conf。对于ubuntu来说，配置文件路径为/etc/nginx。如果你是刚安装的nginx，需要把其中
include /etc/nginx/sites-enabled/*;

注释掉，即加个#
#include /etc/nginx/sites-enabled/*;

这一行会使服务器默认跳转到nginx的欢迎界面，而我们需要指定服务器跳转的首页，就需要把这一行注释掉。
以下代码都要添加在nginx.conf中的http大括号中，而example.com需要替换为你的域名。

## 一、http:no-www跳转到www

```
server {
        listen *:80;
        listen [::]:80;
        server_name example.com;
        return 301 http://www.example.com$request_uri;
}

server {
        listen *:80;
        listen [::]:80;
        server_name www.example.com

        location / {
                 #这里指定服务器跳转首页的路径
                 #一般来说代码如下
                 #root 你的网站根目录;
                 #index index.html;
        }
}
```

## 二、http:www跳转到no-www

```
server {
        listen *:80;
        listen [::]:80;
        server_name www.example.com;
        return 301 http://example.com$request_uri;
}

server {
        listen *:80;
        listen [::]:80;
        server_name example.com

        location / {
                 #这里指定服务器跳转首页的路径
                 #一般来说代码如下
                 #root 你的网站根目录;
                 #index index.html;
        }
}

```

## 三、https:no-www跳转到www

```
server {
        listen *:80;
        listen *:443 ssl; 
        listen [::]:80;
        listen [::]:443 ssl; 
        server_name example.com;

        ssl_certificate ssl证书路径 
        ssl_certificate_key ssl密钥路径 
        return 301 https://www.example.com$request_uri;
}

server {
        listen *:80;
        listen [::]:80;
        server_name www.example.com;
        return 301 https://www.example.com$request_uri;
}

server {
        listen *:443 ssl; 
        listen [::]:443 ssl; 
        server_name www.example.com;      
              
        ssl_certificate ssl证书路径 
        ssl_certificate_key ssl密钥路径 
        location / {
                 #这里指定服务器跳转首页的路径
                 #一般来说代码如下
                 #root 你的网站根目录;
                 #index index.html;
        }
}
```

## 四、https:www跳转到no-www

```
server {
        listen *:80;
        listen *:443 ssl; 
        listen [::]:80;
        listen [::]:443 ssl; 
        server_name www.example.com;

        ssl_certificate ssl证书路径 
        ssl_certificate_key ssl密钥路径 
        return 301 https://example.com$request_uri;
}

server {
        listen *:80;
        listen [::]:80;
        server_name example.com;
        return 301 https://example.com$request_uri;
}

server {
        listen *:443 ssl; 
        listen [::]:443 ssl; 
        server_name example.com;    
                
        ssl_certificate ssl证书路径 
        ssl_certificate_key ssl密钥路径 
        location / {
                 #这里指定服务器跳转首页的路径
                 #一般来说代码如下
                 #root 你的网站根目录;
                 #index index.html;
        }
}
```