

1.下载mysql 镜像
```bash
[root@localhost ~]# docker pull mysql:5.5
Trying to pull repository docker.io/library/mysql ...
```
2.查看本地镜像
```bash
[root@localhost ~]# docker images
REPOSITORY          TAG                 IMAGE ID            CREATED             SIZE
docker.io/mysql     5.5                 f13c4be36ec5        3 weeks ago         205 MB
```

3.根据mysql镜像启动容器
```bash
[root@localhost ~]# docker run -p 3306:3306 --name mysql01 -e MYSQL_ROOT_PASSWORD=123456 -d mysql:5.5
70fb9f7d1ce4d95c6b640a559a099e107678a552340fab49b6539fd69296376d

# mariadb启动方法也是一样的
[root@localhost ~]# docker run -v /Users/wenj91/data/mysql/data:/var/lib/mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456  --name mariadb -d mariadb
```

命令详细介绍：  
 docker run：启动容器  
 -p 3306:3306：映射端口号  
 --name mysql01：启动容器的名称  
 -e MYSQL_ROOT_PASSWORD=123456：设置mysql密码  
 -d mysql:5.5 ：那个镜像  
 -v /data/mysql/data:/var/lib/mysql #挂载本地data目录到 容器中的位置 mysql  