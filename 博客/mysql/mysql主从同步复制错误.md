
## Fatal error: The slave I/O thread stops because master and slave have equal MySQL server ids
[参考](https://www.jb51.net/article/27242.htm)
意思就是从上的server_id和主的一样的，经查看发现从上的/etc/my.cnf中的server_id=1这行我没有注释掉（在下面复制部分我设置了server_id），于是马上把这行注释掉了，然后重启mysql，发现还是报同样的错误。 

使用如下命令查看了一下server_id 
复制代码 代码如下:

mysql> show variables like 'server_id'; 
+---------------+-------+ 
| Variable_name | Value | 
+---------------+-------+ 
| server_id | 1 | 
+---------------+-------+ 
1 row in set (0.00 sec) 

发现，mysql并没有从my.cnf文件中更新server_id，既然这样就只能手动修改了 
复制代码 代码如下:

mysql> set global server_id=2; #此处的数值和my.cnf里设置的一样就行 
mysql> slave start; 

如此执行后，slave恢复了正常。 

不过稍后蚊子使用/etc/init.d/mysqld restart重启了mysql服务，然后查看slave状态，发现又出现了上面的错误，然后查看server_id发现这个数值又恢复到了1。 

之后蚊子又重新查看了一下/etc/my.cnf的内容，确认应该不是这个文件的问题，于是去google查了一下，看到mysql在启动的时候会查找/etc/my.cnf、DATADIR/my.cnf，USER_HOME/my.cnf。 

于是我执行了 
复制代码 代码如下:

find / -name "my.cnf" 

居然在/usr/local/mysql这个目录下发现了my.cnf文件，于是蚊子将这个文件删除了，然后再重启mysql服务，发现一切恢复了正常。如果有人也出现类似的问题，不妨试试这个办法吧。