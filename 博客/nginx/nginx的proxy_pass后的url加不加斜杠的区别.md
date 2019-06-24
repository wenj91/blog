# [nginx配置 proxy_pass](https://blog.csdn.net/abcdocker/article/details/79289626)

nginx配置proxy_pass，需要注意转发的路径配置

第一种：proxy_pass后缀不加斜杠
```
location /abc/ {
            proxy_pass http://172.16.1.38:8080;
     }
```
第二种：proxy_pass后缀加斜杠
```
location /abc/ {
                proxy_pass http://172.16.1.38:8081/;
     } 
```
上面两种配置，区别只在于proxy_pass转发的路径后是否带 /  
针对情况1，如果访问url = http://server/abc/test.jsp，则被nginx代理后，请求路径会便问http://proxy_pass/abc/test.jsp，将test/ 作为根路径，请求test/路径下的资源  
针对情况2，如果访问url = http://server/abc/test.jsp，则被nginx代理后，请求路径会变为 http://proxy_pass/test.jsp，直接访问server的根资源  

典型实例：

```
worker_processes  1;
events {
    worker_connections  1024;
}

http {
    include       mime.types;
    default_type  application/octet-stream;
    sendfile        on;
    keepalive_timeout  65;


upstream app{
    server 172.16.1.38:8233;
}
upstream online{
    server 172.16.1.38:8239;
}


server {
    listen       881;
    server_name  IP;

    location /bxg/user/ {
        root   /root;
        index  index.html index.htm;
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        client_max_body_size 100m;
        proxy_pass  http://online;
解释：当我们访问http://IP/881/bxg/user/下面的资源，nginx会帮我们跳转到online下面对应的IP+端口
此时返回的url =http://IP/881/bxg/user/1.txt
    }


    location /bxg/app/ {
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        client_max_body_size 100m;
        proxy_pass  http://app/;
解释：当我们访问http://IP/881/bxg/app/下面的资源(此时proxy_pass后面带斜杠)，nginx也会帮我们跳转到app下面对应的IP+端口
此时返回的url =http://IP/881/1.txt
    }


#这行属于默认匹配，就是后面什么也不添加，881端口就直接调用这个项目
    location / {
        root   /root;
        index  index.html index.htm;
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        client_max_body_size 100m;
        proxy_pass  http://app;
    }
}
```

提示：这种location常用于只有一个公网IP和端口场景，内网IP没有进行映射，但是又需要请求我们的内网服务器的服务，就可以使用location的模式。
--------------------- 
作者：abcdocker 
来源：CSDN 
原文：https://blog.csdn.net/abcdocker/article/details/79289626 
版权声明：本文为博主原创文章，转载请附上博文链接！