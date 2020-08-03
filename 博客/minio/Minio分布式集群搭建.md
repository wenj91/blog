# [Minio分布式集群搭建](https://blog.csdn.net/ywd1992/article/details/82385101)

### 文章目录

*   [一、分布式Minio快速入门](#)

*   [1、分布式Minio有什么好处?](#)

*   [二、Minio分布式集群搭建](#)

*   [1、获取Minio](#)
*   [2、修改主机名及hosts](#)
*   [3、系统最大文件数修改](#)
*   [4、目录创建](#)
*   [5、集群启动文件](#)
*   [6、minio.service](#)
*   [7、二进制文件](#)
*   [8、权限修改](#)
*   [9、启动集群](#)
*   [10、代理集群](#)
*   [11、测试](#)

一、分布式Minio快速入门
==============

分布式Minio可以让你将多块硬盘（甚至在不同的机器上）组成一个对象存储服务。由于硬盘分布在不同的节点上，分布式Minio避免了单点故障。

1、分布式Minio有什么好处?
----------------

在大数据领域，通常的设计理念都是无中心和分布式。Minio分布式模式可以帮助你搭建一个高可用的对象存储服务，你可以使用这些存储设备，而不用考虑其真实物理位置。

数据保护

分布式Minio采用 erasure code（纠删码）来防范多个节点宕机和位衰减bit rot。

分布式Minio至少需要4个节点，使用分布式Minio自动引入了纠删码功能。

高可用

单机Minio服务存在单点故障，相反，如果是一个N节点的分布式Minio,只要有N/2节点在线，你的数据就是安全的。不过你需要至少有N/2+1个节点 Quorum 来创建新的对象。

例如，一个8节点的Minio集群，每个节点一块盘，就算4个节点宕机，这个集群仍然是可读的，不过你需要5个节点才能写数据。

限制

分布式Minio单租户存在最少4个盘最多16个盘的限制（受限于纠删码）。这种限制确保了Minio的简洁，同时仍拥有伸缩性。如果你需要搭建一个多租户环境，你可以轻松的使用编排工具（Kubernetes）来管理多个Minio实例。

注意，只要遵守分布式Minio的限制，你可以组合不同的节点和每个节点几块盘。比如，你可以使用2个节点，每个节点4块盘，也可以使用4个节点，每个节点两块盘，诸如此类。

一致性

Minio在分布式和单机模式下，所有读写操作都严格遵守read-after-write一致性模型。

二、Minio分布式集群搭建
==============

生产环境建议最少4节点



| 节点| IP| data|  
|---|---|---|  
|minio1|10.10.0.1|/data/minio/data|
|minio2|0.10.0.2|/data/minio/data|
|minio3|10.10.0.3|/data/minio/data|
|minio4|10.10.0.4|/data/minio/data|



1、获取Minio
---------

https://dl.min.io/server/minio/release/linux-amd64/minio

2、修改主机名及hosts
-------------

    hostnamectl set-hostname minio1
    hostnamectl set-hostname minio2
    hostnamectl set-hostname minio3
    hostnamectl set-hostname minio4
    ...
    

    cat >> /etc/hosts <<EOF
    10.10.0.1 minio1
    10.10.0.2 minio2
    10.10.0.3 minio3
    10.10.0.4 minio4
    EOF
    

3、系统最大文件数修改
-----------

    echo "*   soft    nofile  65535" >> /etc/security/limits.conf
    echo "*   hard    nofile  65535" >> /etc/security/limits.conf
    

4、目录创建
------

*   启动脚本及二进制文件目录 run
*   数据存储目录 data
*   配置文件目录/etc/minio

    mkdir -p /data/minio/{run,data} && mkdir -p /etc/minio
    

5、集群启动文件
--------

    vim /data/minio/run/run.sh
    

*   MINIO\_ACCESS\_KEY：用户名，长度最小是5个字符
*   MINIO\_SECRET\_KEY：密码，密码不能设置过于简单，不然minio会启动失败，长度最小是8个字符
*   –config-dir：指定集群配置文件目录

    #!/bin/bash
    export MINIO_ACCESS_KEY=Minio
    export MINIO_SECRET_KEY=Test1234!
    
    /data/minio/run/minio server --config-dir /etc/minio \
    http://10.10.0.1/data/minio/data \
    http://10.10.0.2/data/minio/data \
    http://10.10.0.3/data/minio/data \
    http://10.10.0.4/data/minio/data \
    

6、minio.service
---------------

*   WorkingDirectory：二进制文件目录
*   ExecStart：指定集群启动脚本

    cat > /usr/lib/systemd/system/minio.service <<EOF
    [Unit]
    Description=Minio service
    Documentation=https://docs.minio.io/
    
    [Service]
    WorkingDirectory=/data/minio/run/
    ExecStart=/data/minio/run/run.sh
    
    Restart=on-failure
    RestartSec=5
    
    [Install]
    WantedBy=multi-user.target
    EOF
    

7、二进制文件
-------

将minio二进制文件上传到/data/minio/run目录

8、权限修改
------

给所有涉及到的文件或目录添加权限

*   service文件
*   二进制文件
*   集群启动脚本

    chmod +x /usr/lib/systemd/system/minio.service && chmod +x /data/minio/run/minio && chmod +x /data/minio/run/run.sh
    

9、启动集群
------

    systemctl daemon-reload
    systemctl enable minio && systemctl start minio
    

10、代理集群
-------

生产环境需要使用Nginx将集群地址进行代理，对外统一入口

    upstream minio{
            server 10.10.0.1:9000;
            server 10.10.0.2:9000;
            server 10.10.0.3:9000;
            server 10.10.0.4:9000;
    }
    server {
            listen 9000;
            server_name minio;
            location / {
                    proxy_pass http://minio;
                    proxy_set_header Host $http_host;
                    client_max_body_size 1000m;
            }
    }
    

11、测试
-----

浏览器访问minio集群代理地址+9000端口，用户名密码为上文中启动文件run.sh中我们设置的

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200407111842400.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3l3ZDE5OTI=,size_16,color_FFFFFF,t_70)