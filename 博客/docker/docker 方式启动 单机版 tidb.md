# [以docker 方式启动 单机版 tidb](https://blog.csdn.net/freewebsys/article/details/70843679)


## 1，关于tidb

tidb 其灵感来自于 Google 的 F1 和 Google spanner, TiDB 支持包括传统 RDBMS 和 NoSQL 的特性。
sql 完全支持mysql，同时人家还是一个分布式数据库。
什么分库分表都弱爆了，这个直接分，超级方便。而且还是开源的。
是国内的 技术大牛 黄东旭 的公司 pincap 开发的。
就是之前写 codis 那个人。
https://github.com/pingcap/tidb
很厉害的人，设计的很好的项目。


## 2，tidb安装&启动

```bash
docker pull pingcap/tidb
#45.58 MB
mkdir -p /data/tidb/data
docker run --name tidb-server -d -v /data/tidb/data:/tmp/tidb -p 4000:4000 -p 10080:10080 pingcap/tidb:latest
#设置数据文件，默认使用 goleveldb 存储。
```

启动成功默认端口 4000 ，也可以伪装成mysql，把端口修改成3306 。
```bash
# mysql -h 127.0.0.1 -P 4000 -u root -D test --prompt="tidb> "
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MySQL connection id is 3
Server version: 5.7.1-TiDB-1.0 MySQL Community Server (GPL)

Copyright (c) 2000, 2016, Oracle, MariaDB Corporation Ab and others.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

tidb> 
```

登录成功，Server version: 5.7.1-TiDB-1.0 MySQL Community Server (GPL) tidb。
可以使用 10080 端口查看状态信息：

```bash
# curl localhost:10080/status
{"connections":1,"version":"5.7.1-TiDB-1.0","git_hash":"31bc1083fc9195181d187639efb847d19037d9de"}
```

感觉上应该是集群的时候使用的。

## 3，创建数据库&用户

创建数据库 demo 并创建用户 demo 赋值权限。
注意：这些sql 语句在 mysql & tidb 当中都key执行并成功分配权限&登录成功。

```mysql
CREATE DATABASE demo CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'demo'@'%' IDENTIFIED BY 'demo';
GRANT ALL PRIVILEGES ON demo.* TO 'demo'@'%';
FLUSH PRIVILEGES;
```

使用golang进行数据库插入&查询数据：

```go
package main

import (
    "fmt"
    _ "github.com/go-sql-driver/mysql"
    "database/sql"
    "time"
    "strconv"
)
func main() {
    db, err := sql.Open("mysql", "demo:demo@tcp(127.0.0.1:4000)/demo")
    fmt.Println(db, err)
    //
    start := time.Now()
    loop := 10000
    for i := 0; i < loop; i ++ {
        result, err := db.Exec(
            "INSERT INTO users(`name`, age) VALUES (?, ?)",
            "user"+strconv.Itoa(i),
            i,
        )
        if i%(loop/10) == 0 {
            fmt.Println(result, err)
        }
    }
    end := time.Now()
    fmt.Println("测试插入时间:", end.Sub(start).Seconds())
}
```

## 4，总结

本文的原文连接是: http://blog.csdn.net/freewebsys/article/details/70843679 未经博主允许不得转载。
博主地址是：http://blog.csdn.net/freewebsys

tidb 是非常好的maridb的替代的产品。
可以完全的兼容 sql 查询啊，插入啊，join 啊。
同时支持 TB 级别的数据存储。机器多的话速度就快了。
而 mariadb 还是 以前的老思维，不支持分布的不熟。
tidb 扩展起来也很方便。关键是代码不用修改了，一切都交个运维吧。
写完了就可以早回家洗洗睡了。
————————————————
版权声明：本文为CSDN博主「freewebsys」的原创文章，遵循CC 4.0 BY-SA版权协议，转载请附上原文出处链接及本声明。
原文链接：https://blog.csdn.net/freewebsys/article/details/70843679