# docker查看宿主机ip

```bash
docker inspect --format '{{ .NetworkSettings.IPAddress }}' <container-ID> 

# 或
docker inspect <container id> 

# 或
docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' container_name_or_id
```
--------------------- 
作者：呜呜呜啦啦啦 
来源：CSDN 
原文：https://blog.csdn.net/u013360850/article/details/79878991 
版权声明：本文为博主原创文章，转载请附上博文链接！