# [docker安装redis 指定配置文件且设置了密码](https://www.cnblogs.com/cgpei/p/7151612.html)

---------首先，所有docker的命令，都可以用 docker help 来查询，这个挺好的，我反正记不住辣么多命令呀。
 
1、直接pull 官方镜像吧。没啥说的，这样方便省事。如果你非要用啥Dockerfile，那么你高兴就好。
 
2、然后创建一个 redis/data 目录，如果需要指定配置文件，那么请在redis目录下放一个redis.conf配置文件。配置文件去redis安装包中找一个，哈哈哈。
 
3、然后启动容器，做映射。
　　3.1、端口映射，data目录映射，配置文件映射。
　　# docker run -p 6699:6379 --name myredis -v $PWD/redis.conf:/etc/redis/redis.conf -v $PWD/data:/data -d redis:3.2 redis-server /etc/redis/redis.conf --appendonly yes

　　命令说明：
　　--name myredis : 指定容器名称，这个最好加上，不然在看docker进程的时候会很尴尬。
　　-p 6699:6379 ： 端口映射，默认redis启动的是6379，至于外部端口，随便玩吧，不冲突就行。
　　-v $PWD/redis.conf:/etc/redis/redis.conf ： 将主机中当前目录下的redis.conf配置文件映射。
　　-v $PWD/data:/data -d redis:3.2 ： 将主机中当前目录下的data挂载到容器的/data
　　--redis-server --appendonly yes :在容器执行redis-server启动命令，并打开redis持久化配置\
　　注意事项：
　　　　如果不需要指定配置，-v $PWD/redis.conf:/etc/redis/redis.conf 可以不用 ，
　　　　redis-server 后面的那段 /etc/redis/redis.conf 也可以不用。
　　　　主要我是用来给redis设置了密码，我怕别人偷偷用我的redis。哈哈哈
 
4、如果顺利的话，你的redis容器已经正常启动啦。那么现在可以docker ps 看看这个进程，然后连上去看看。
　　4.1、直接连接到redis容器中，直接上命令：
　　　　# docker inspect myredis | grep IP ---先查询到myredis容器的ip地址。
　　　　# docker run -it redis:3.2 redis-cli -h 192.168.42.32 ---连接到redis容器。然后就进入redis命令行了。
 
　　4.2、直接通过本机的ip端口连接到redis，继续看下面：
　　　　注意： 由于我之前已经安装过redis，非docker方式的，所以我有redis-cli的客户端。
　　　　先进入了我之前安装的redis目录，然后执行下面的命令:
　　　　# ./bin/redis-cli -p 6699 ---因为我用的6699的本机端口映射到的redis容器，所以指定6699去连接。