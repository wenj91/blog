# windows环境安装MySQL或者MariaDB后（比如XAMPP整合包），数据库里的中文在网页上显示？？？

解决方法如下：

找到mysql安装目录

找到my.ini 这个文件，打开

在[mysqld] 下 增加一句 character_set_server=utf8

这句是解决中文乱码的。

再增加一句

lower_case_table_names=2

注: 1 表示不区分大小写 2表示区分大小写
例如这样

[mysqld]
datadir=C:/Program Files/MariaDB 5.5/data
port=3306
character_set_server=utf8

lower_case_table_names=2
sql_mode=”STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION”
default_storage_engine=innodb
innodb_buffer_pool_size=497M
innodb_log_file_size=50M
[client]
port=3306

重启服务 OK。