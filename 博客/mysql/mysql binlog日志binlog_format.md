# [mysql binlog日志binlog_format](https://blog.csdn.net/itliwei/article/details/84915411)

binlog\_format 查看：

    show variables like 'binlog_format';
    +---------------+-------+
    | Variable_name | Value |
    +---------------+-------+
    | binlog_format | ROW   |
    +---------------+-------+
    1 row in set (0.01 sec)
    

binlog\_format 有三种：ROW,STATEMENT,MIXID  
我本地安装的是8.0版本，默认为MIXID.

修改配置方式：  
找到my.cnf,修改binlog\_format:

    #ROW,每一行记录修改都会记录binlog,数据清晰，便于理解，但是日志量会非常大，增加IO负担
    #STATEMENT，每一条会修改数据的SQL都会记录binlog,优点是日志量会减少，提高IO性能，缺点是slaver端会执行同样的SQL，为了保证一致性需要存储SQL执行的上下文信息，还有一些复杂的应用场景（存储过程、触发器、特殊命令）等，可能会出现问题
    #MIXED介于ROW和STATEMENT之间，根据具体SQL区分对待来记录不同的日志
    binlog_format=ROW
    

这种方式修改后需要重启mysql。

除此之外， 还有一种运行时修改binlog\_format:

    SET SESSION binlog_format = 'ROW';