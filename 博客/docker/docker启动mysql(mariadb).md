

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

# mariadb启动方法也是一样的
[root@localhost ~]# docker run -v /mnt/h/data/mariadb/data:/var/lib/mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456  -e MYSQL_ROOT_HOST=127.0.0.1 --name mariadb --ulimit nofile=65536:65536 -d mariadb

docker run -v d:/data/mariadb/data:/var/lib/mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456  -e MYSQL_ROOT_HOST=127.0.0.1 --name mariadb --ulimit nofile=65536:65536 -d mariadb:10.3.14

docker run -v /Users/wenj91/data/mysql:/var/lib/mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456  --name mariadb --ulimit nofile=65536:65536 -d mariadb

docker run --name tidb-server -d -v /Users/wenj91/data/tidb:/tmp/tidb -p 4000:4000 -p 10080:10080 pingcap/tidb:latest

docker run -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456  -e MYSQL_ROOT_HOST=127.0.0.1 --name mariadb --ulimit nofile=65536:65536 -d mariadb

 mysql -P3307  --protocol=TCP  -uroot -p

 docker run -v /Users/wenj91/data/mysql:/var/lib/mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456 --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci --name mysql --ulimit nofile=65536:65536 -d mysql:latest

 docker run --name mysql8.0.21 -v /Users/wenj91/data/mysql2:/var/lib/mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456 -d mysql:8.0.21 --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci


```

命令详细介绍：  
 docker run：启动容器  
 -p 3306:3306：映射端口号  
 --name mysql01：启动容器的名称  
 --ulimit nofile=65536:65536
 -d mysql:5.5 ：那个镜像  
 -v /data/mysql/data:/var/lib/mysql #挂载本地data目录到 容器中的位置 mysql  
 -e MYSQL_ROOT_PASSWORD=123456：设置mysql密码  
 -e MYSQL_USER=xx
 -e MYSQL_PASSWORD=testpwd
 -e MYSQL_ROOT_HOST=%
 