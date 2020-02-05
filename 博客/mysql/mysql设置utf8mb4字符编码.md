# [mysql设置utf8mb4字符编码](https://blog.csdn.net/javandroid/article/details/81235387)

## 一、utf8和utf8mb4字符编码

## 二、查看mysql数据库编码

### 1.查看编码

#显示所有编码和字符校对的参数
SHOW VARIABLES WHERE Variable_name LIKE 'character_set_%' OR Variable_name LIKE 'collation%';
1
2
总共有以下这些项：

Variable_name	Value
character_set_client	utf8mb4
character_set_connection	utf8mb4
character_set_database	utf8mb4
character_set_filesystem	binary
character_set_results	utf8mb4
character_set_server	utf8mb4
character_set_system	utf8
collation_connection	utf8mb4_unicode_ci
collation_database	utf8mb4_unicode_ci
collation_server	utf8mb4_unicode_ci


1、character_set_client
　　主要用来设置客户端使用的字符集。

2、character_set_connection
　　主要用来设置连接数据库时的字符集，如果程序中没有指明连接数据库使用的字符集类型则按照这个字符集设置。

3、character_set_database
　　主要用来设置默认创建数据库的编码格式，如果在创建数据库时没有设置编码格式，就按照这个格式设置。

4、character_set_filesystem
　　文件系统的编码格式，把操作系统上的文件名转化成此字符集，即把 character_set_client转换character_set_filesystem， 默认binary是不做任何转换的。

5、character_set_results
　　数据库给客户端返回时使用的编码格式，如果没有指明，使用服务器默认的编码格式。

6、character_set_server
　　服务器安装时指定的默认编码格式，这个变量建议由系统自己管理，不要人为定义。

7、character_set_system
　　数据库系统使用的编码格式，这个值一直是utf8，不需要设置，它是为存储系统元数据的编码格式。

8、character_sets_dir
　　这个变量是字符集安装的目录。

我们只关注下列变量是否符合我们的要求

```ini
character_set_client
character_set_connection
character_set_database
character_set_results
character_set_server
```

下列三个系统变量我们不需要关心，不会影响乱码等问题
character_set_filesystem
character_set_system
character_sets_dir

## 三、修改编码为utf8mb4

修改my.cnf文件。加入以下内容，然后重启数据库：systemctl restart mysqld


```ini
[mysqld]
character-set-server=utf8mb4

[mysql]
default-character-set=utf8mb4

[client]
default-character-set=utf8mb4
```

## 四、已有的库和表更改为utf8mb4

以上方式更改编码对于已有的库和表是不产生影响的，需要我们单独进行转换。

```mysql
#更改数据库编码：
ALTER DATABASE 数据库名 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

#更改表编码：
ALTER TABLE 表名CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci; 
如有必要，还可以更改列的编码
```

参考：创建支持emoji表情的MySQL数据库（utf8mb4）
————————————————
版权声明：本文为CSDN博主「爪哇探索者」的原创文章，遵循 CC 4.0 BY-SA 版权协议，转载请附上原文出处链接及本声明。
原文链接：https://blog.csdn.net/javandroid/article/details/81235387