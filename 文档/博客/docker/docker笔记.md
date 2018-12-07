
# docker

## docker常用命令
 1. docker search mysql   这条命令表示查询mysql的所有镜像信息
 2. docker pull mysql  表示从官方下载默认版本的mysql，latest
    docker pull mysql:5.5  表示下载mysql版本5.5的
 3. docker images 查看当前本地的所有镜像
 4. docker rmi image-id   删除制定镜像，image-id是每个镜像独有的id
 5. docker rum ......    根据镜像启动容器
 6. docker ps            查看运行中的容器
 7. docker ps -a         查看所有容器
 8. docker start 容器id   启动容器
 9. docker stop  容器id   停止容器
10. docker rm    容器id   删除容器
11. service firewalld status   查看防火墙状态
12. service firewalld stop     关闭防火墙

## docker设置容器固定ip
* 查看docker网络类型:   
`docker network ls`

* 创建自定义网络类型，并且指定网段:  
`docker network create --subnet=192.168.0.0/16 staticnet`

* 使用新的网络类型创建并启动容器:  
`docker run -it --name userserver --net staticnet --ip 192.168.0.2 ubuntu /bin/bash`

`docker run -it --name dxf --net bridge centos /bin/bash`

docker run -it --name dxf --privileged --net host -m 1G --memory-swap 3G centos_dxf /bin/bash

--privileged



* macvlan
docker network create -d macvlan \
  --subnet=172.16.86.0/24 \
  --gateway=172.16.86.1  \
  -o parent=en0 pub_net

[参考](https://blog.csdn.net/wanghao_0206/article/details/79583325)  
[官方参考](https://docs.docker.com/network/macvlan/#bridge-mode)

docker ps -a 列举所有进程

docker start xx
docker stop xx
docker rm xx

docker attach xx


docker run -p 3306:3306 --name mysql01 -e MYSQL_ROOT_PASSWORD=root -d mysql:5.5

## docker重新命名容器
`docker container rename oriName newName`

## docker容器与宿主机互相拷贝
➜  ~ docker cp --help

Usage:	docker cp [OPTIONS] CONTAINER:SRC_PATH DEST_PATH|-
	docker cp [OPTIONS] SRC_PATH|- CONTAINER:DEST_PATH

Copy files/folders between a container and the local filesystem

Options:
  -a, --archive       Archive mode (copy all uid/gid information)
  -L, --follow-link   Always follow symbol link in SRC_PATH

ps:  
`docker cp /Users/wenj91/xx centos:/home/`   
将主机中`/Users/wenj91/xx`目录拷贝到容器中`/home`目录

