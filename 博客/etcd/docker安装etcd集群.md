# [docker安装etcd集群](http://www.zhongruitech.com/561959524.html)
etcd是一个高可用的键值存储系统，主要用于共享配置和服务发现。etcd是由CoreOS开发并维护的，灵感来自于 ZooKeeper 和 Doozer，它使用Go语言编写，并通过Raft一致性算法处理日志复制以保证强一致性。Raft是一个来自Stanford的新的一致性算法，适用于分布式系统的日志复制，Raft通过选举的方式来实现一致性，在Raft中，任何一个节点都可能成为Leader。Google的容器集群管理系统Kubernetes、开源PaaS平台Cloud Foundry和CoreOS的Fleet都广泛使用了etcd。

etcd的特性如下：

　　　　简单: curl可访问的用户的API（HTTP+JSON）定义明确，面向用户的API（gRPC）

　　　　安全: 可选的SSL客户端证书认证

　　　　快速: 单实例每秒 1000 次写操作

　　　　可靠: 使用Raft保证一致性

Etcd构建自身高可用集群主要有三种形式:
　　　　1）静态发现: 预先已知 Etcd 集群中有哪些节点，在启动时直接指定好Etcd的各个node节点地址
　　　　2）Etcd动态发现: 通过已有的Etcd集群作为数据交互点，然后在扩展新的集群时实现通过已有集群进行服务发现的机制
　　　　3）DNS动态发现: 通过DNS查询方式获取其他节点地址信息

docker
## IP:
    服务器A：192.167.0.168
    服务器B：192.167.0.170
    服务器C：192.167.0.172
首先在各个服务器上下载最新的etcd镜像

```bash
# docker pull quay.io/coreos/etcd
```

在一台机器配置了3个容器，在机器上创建了子网络，三台容器在一个网络里

```bash
# docker network create --subnet=192.167.0.0/16 etcdnet
```

创建集群：将三个服务器统一添加进集群

A中执行:

```bash
# docker run -d -p 2379:2379 -p 2380:2380 --restart=always --net etcdnet --ip 192.167.0.168 --name etcd0 quay.io/coreos/etcd /usr/local/bin/etcd --name autumn-client0 -advertise-client-urls http://192.167.0.168:2379 -listen-client-urls http://0.0.0.0:2379 -initial-advertise-peer-urls http://192.167.0.168:2380 -listen-peer-urls http://0.0.0.0:2380 -initial-cluster-token etcd-cluster -initial-cluster autumn-client0=http://192.167.0.168:2380,autumn-client1=http://192.167.0.170:2480,autumn-client2=http://192.167.0.172:2580 -initial-cluster-state new
```

B中执行:

```bash
# docker run -d -p 2479:2479 -p 2480:2480 --restart=always --net etcdnet --ip 192.167.0.170  --name etcd1 quay.io/coreos/etcd /usr/local/bin/etcd --name autumn-client1 -advertise-client-urls http://192.167.0.170:2479 -listen-client-urls http://0.0.0.0:2479 -initial-advertise-peer-urls http://192.167.0.170:2480 -listen-peer-urls http://0.0.0.0:2480 -initial-cluster-token etcd-cluster -initial-cluster autumn-client0=http://192.167.0.168:2380,autumn-client1=http://192.167.0.170:2480,autumn-client2=http://192.167.0.172:2580 -initial-cluster-state new
```

C中执行：

```bash
# docker run -d -p 2579:2579 -p 2580:2580 --restart=always --net etcdnet --ip 192.167.0.172  --name etcd2 quay.io/coreos/etcd /usr/local/bin/etcd --name autumn-client2 -advertise-client-urls http://192.167.0.172:2579 -listen-client-urls http://0.0.0.0:2579 -initial-advertise-peer-urls http://192.167.0.172:2580 -listen-peer-urls http://0.0.0.0:2580 -initial-cluster-token etcd-cluster -initial-cluster autumn-client0=http://192.167.0.168:2380,autumn-client1=http://192.167.0.170:2480,autumn-client2=http://192.167.0.172:2580 -initial-cluster-state new
```

注意：代码中可能格式会有错误，请参照A中的代码手动修改后执行。

## 集群验证
进入docker：

使用docker exec命令
这个命令使用exit命令后，不会退出后台，一般使用这个命令，使用方法如下

```bash
docker exec -it db3 /bin/sh 或者 docker exec -it d48b21a7e439 /bin/sh
```

root 权限进入docker

```bash
docker exec -it --user root <container id> /bin/sh
```

DB3或者d48b21a7e439 为docker的name和ID,使用时修改为自己docker的信息。 
查询已运行的docker容器基本信息  ：      docker ps

docker 中添加信息： （注意：v3 和v2版本的etcd原理不同，操作也不同，此处为v3）

```bash
[root@localhost etcd]# etcdctl put mykey "this is awesome"
No help topic for 'put' # etcdctl 需要设置环境变量 ETCDCTL_API=3,使用第三版的api，默认的api是2
[root@localhost etcd]# ETCDCTL_API=3 etcdctl put mykey "this is awesome"
OK
```

设置环境变量：

```bash
root@localhost etcd]# echo 'export ETCDCTL_API=3' >> /etc/profile ## 环境变量添加 ETCDCTL_API=3
[root@localhost etcd]# source /etc/profile # 是profile中修改的文件生效
[root@localhost etcd]# etcdctl get mykey # 可以直接使用./etcdctl get key 命令了
mykey
this is awesome # 刚添加的内容 
```