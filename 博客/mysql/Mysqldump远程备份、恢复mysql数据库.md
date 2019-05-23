# [Mysqldump远程备份、恢复mysql数据库](https://blog.csdn.net/xizaihui/article/details/53084080)

1、远程地址 
直接上shell脚本

```bash
#!/bin/bash

d=`date +'%Y%m%d_%H_%M_%S'`

cd /mnt/mysql_db

mysqldump -h mysql.rds.aliyuncs.com -u user -p'passwd'  dbname | gzip > dbname.sql.gz_$d

rm -rf /mnt/mysql_db/cgwy.sql.gz_`date -d '-10 days' +%Y%m%d`_*
```

其中 -h 后面是mysql远程地址，比如阿里云的RDS，dbname是数据库名字 
脚本是保存10天的数据，10天之前的会删掉。

如果是远程ip

则是这条命令：

```bash
# mysqldump -h 192.168.1.1 -u user -p'passwd'  dbname | gzip > dbname.sql.gz_$d
```

替换就行

将远程备份的数据库恢复到本地mysql中

首先解压
```bash
cd /mnt/mysql_db

# gunzip -c cgwy.sql.gz_20161108_07_05_16 > cgwy.sql
```

然后执行：

```bash
# mysql -uroot -p123456  cgwy < /mnt/mysql_db/cgwy.sql
```

此时会警告：
# Warning: Using a password on the command line interface can be insecure.
可以忽略不管，坐等数据恢复，数据上10G以上，需要一段时间。 
如果追求完美，不想出现这个警告，编辑my.cnf,在[mysqld]段中添加：

# vim /etc/my.cnf

[mysqld]

user=root
password=123456