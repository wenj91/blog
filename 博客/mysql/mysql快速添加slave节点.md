
# [MySQL Replication without stopping master](https://dba.stackexchange.com/questions/35977/mysql-replication-without-stopping-master)

[参考2](https://www.electricmonk.nl/log/2016/11/06/very-fast-mysql-slave-setup-with-zero-downtime-using-rsync/)

here are two approaches you can try with no or minimal downtime

Given the following:

Master IP is 10.1.20.30
Slave IP is 10.1.20.40
APPROACH #1 : Data is 100% InnoDB
This is very straightforward.

STEP01) If the Master does not have server-id defined in my.cnf you will have to add it

[mysqld]
server-id=100
STEP02) If the Master does not have log-bin defined in my.cnf you will have to add it

[mysqld]
log-bin=mysql-bin
STEP03) If you have do Steps 1 and/or 2 on the Master, do service mysql restart (mandatory)

STEP04) Create MySQL Replication User on the Master

mysql> GRANT SELECT,REPLICATION USER,REPLICATION CLIENT ON *.*
TO repluser@'10.1.2.30' IDENTIFIED BY 'replpass';
STEP05) Create a mysqldump as a point-in-time snapshot on the Master

MYSQL_CONN="-uroot -ppassword"
MYSQLDUMP_OPTIONS="--master-data=1 --single-transaction --flush-privileges"
MYSQLDUMP_OPTIONS="${MYSQLDUMP_OPTIONS} --routines --triggers --all-databases"
mysqldump ${MYSQL_CONN} ${MYSQLDUMP_OPTIONS} > MySQLData.sql 
When done, line 22 of MySQLData should have the binary log and position of the Master as of the moment the mysqldump was launched. To see it, just run

head -22 MySQLData.sql | tail -1
STEP06) Create replication status on the Slave with

CHANGE MASTER TO
MASTER_HOST='10.1.20.30',
MASTER_PORT=3306,
MASTER_USER='repluser',
MASTER_PASSWORD='replpass',
MASTER_LOG_FILE='mysql-bin.000001',
MASTER_LOG_POS=4;
STEP 07) Load the mysqldump into the Slave

mysql -u... -p... < MySQLData.sql 
Don't worry about replication starting at the right place. Remember, I said line 22 contains the command with correct binary log and position.

STEP 08) Run SHOW SLAVE STATUS\G

If Slave_IO_Running is Yes and Slave_SQL_Running is Yes, CONGRATULATIONS !!!

APPROACH #2 : Data is InnoDB/MyISAM Mix
Rather than reinvent the wheel, please read my earlier posts on using rsync to make a Slave

Jul 08, 2011 - MySQL slave replication reset with no Master Downtime (using MyISAM)
May 23, 2011 - How can I move a database from one server to another?
Apr 08, 2011 - Create a MySQL slave from another slave, but point it at the master
Give it a Try !!!


```
# dump一份主节点数据
mysqldump -uroot -p123456 --master-data=1 --single-transaction --flush-privileges --routines --triggers --all-databases > MySQLData.sql

# 查看dump数据信息
head -22 MySQLData.sql | tail -1
# 控制台输出
CHANGE MASTER TO MASTER_LOG_FILE='mysql-bin.000005', MASTER_LOG_POS=342;

# 创建日志表
CREATE TABLE MY_TABLE (
  File VARCHAR,
  Position BIGINT UNSIGNED,
  Binlog_Do_DB VARCHAR,
  Binlog_Ignore_DB VARCHAR
);
INSERT INTO MY_TABLE(File, Position) VALUES ('mysql-bin.000001', 1061);

# 从库指向主库
CHANGE MASTER TO
MASTER_HOST='docker.for.mac.host.internal',
MASTER_PORT=3306,
MASTER_USER='root',
MASTER_PASSWORD='123456',
MASTER_LOG_FILE='mysql-bin.000001',
MASTER_LOG_POS=1061;

# 从库配置
slave--my.cnf
[mysqld]
server-id=2
relay-log=slave-relay-bin
relay-log-index=slave-relay-bin.index

# 重置
RESET SLAVE;

# 开始备份
start slave;

show variables like '%server_id%';

CHANGE MASTER TO
MASTER_HOST='docker.for.mac.host.internal',
MASTER_PORT=3306,
MASTER_USER='root',
MASTER_PASSWORD='123456',
MASTER_LOG_FILE='mysql-bin.000001',
MASTER_LOG_POS=1061;

SHOW VARIABLES LIKE 'log_error';

SHOW VARIABLES LIKE '%error%';

show variables like '%log%';

show master status;

show slave status;
```


docker cp /Users/wenj91/MySQLData.sql mariadb_slave:/home/

docker exec -it mariadb mysql -uroot -p123456 < /home/MySQLData.sql

docker run -v /Users/wenj91/data/mysql/data:/var/lib/mysql -p 3306:3306 -e MYSQL_ROOT_PASSWORD=123456  --name mariadb -d mariadb

## log pos
grep -R log_error /etc/mysql/*
