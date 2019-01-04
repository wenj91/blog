# [docker mysql主从配置](https://www.cnblogs.com/w2206/p/6963065.html)

## my.cnf配置
* 主节点my.cnf
```
[mysqld]
server_id = 1
log-bin= mysql-bin
read-only=0
binlog-do-db=blogging
replicate-ignore-db=mysql
replicate-ignore-db=sys
replicate-ignore-db=information_schema
replicate-ignore-db=performance_schema
!includedir /etc/mysql/conf.d/
!includedir /etc/mysql/mysql.conf.d/
```
* 从节点my.cnf
```
[mysqld]
server_id = 2
log-bin= mysql-bin
read-only=1
binlog-do-db=blogging
replicate-ignore-db=mysql
replicate-ignore-db=sys
replicate-ignore-db=information_schema
replicate-ignore-db=performance_schema
!includedir /etc/mysql/conf.d/
!includedir /etc/mysql/mysql.conf.d/
```

说明：   
log-bin ：需要启用二进制日志 
server_id : 用于标识不同的数据库服务器，而且唯一    
binlog-do-db : 需要记录到二进制日志的数据库   
binlog-ignore-db : 忽略记录二进制日志的数据库   
auto-increment-offset :该服务器自增列的初始值   
auto-increment-increment :该服务器自增列增量      
replicate-do-db ：指定复制的数据库   
replicate-ignore-db ：不复制的数据库   
relay_log ：从库的中继日志，主库日志写到中继日志，中继日志再重做到从库   
log-slave-updates ：该从库是否写入二进制日志，如果需要成为多主则可启用。只读可以不需要  
如果为多主的话注意设置 auto-increment-offset 和 auto-increment-increment   
如上面为双主的设置：   
服务器 152 自增列显示为：1,3,5,7,……（offset=1，increment=2）     
服务器 153 自增列显示为：2,4,6,8,……（offset=2，increment=2）    

## docker启动创建主从容器
* 主节点容器
`docker run --name mastermysql -d -p 3307:3306 -e MYSQL_ROOT_PASSWORD=anech -v d:/docker/mysql/master/data:/var/lib/mysql -v d:/docker/mysql/master/conf/my.cnf:/etc/mysql/my.cnf  mysql`  

* 从节点容器
`docker run --name slavermysql -d -p 3308:3306 -e MYSQL_ROOT_PASSWORD=anech -v d:/docker/mysql/slaver/data:/var/lib/mysql -v d:/docker/mysql/slaver/conf/my.cnf:/etc/mysql/my.cnf  mysql` 

这里为了方便查看数据，把Docker的端口都与本机进行了映射,对应的本地master/data文件夹和slaver/data文件夹下也能看到同步的数据库文件  

* 主从配置
```
//进入master容器

docker exec -it mastermysql bash
//启动mysql命令，刚在创建窗口时我们把密码设置为：anech

mysql -u root -p

//创建一个用户来同步数据
GRANT REPLICATION SLAVE ON *.* to 'backup'@'%' identified by '123456';

//这里表示创建一个slaver同步账号backup，允许访问的IP地址为%，%表示通配符
//例如：192.168.0.%表示192.168.0.0-192.168.0.255的slaver都可以用backup用户登陆到master上

//查看状态，记住File、Position的值，在Slaver中将用到
show master status;


//进入slaver容器
docker exec -it slavermysql bash

//启动mysql命令，刚在创建窗口时我们把密码设置为：anech
mysql -u root -p

//设置主库链接
change master to master_host='172.17.0.2',master_user='backup',master_password='123456',master_log_file='mysql-bin.000001',master_log_pos=0,master_port=3306;

//启动从库同步
start slave;

//查看状态
show slave status\G;
```

说明：  
master_host：主库地址  
master_user：主库创建的同步账号    
master_password：主库创建的同步密码   
master_log_file：主库产生的日志    
master_log_pos：主库日志记录偏移量  
master_port：主库使用的端口，默认为3306