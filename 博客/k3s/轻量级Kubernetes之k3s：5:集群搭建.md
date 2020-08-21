# [轻量级Kubernetes之k3s：5:集群搭建](https://blog.csdn.net/liumiaocn/article/details/103268634)

![在这里插入图片描述](https://img-blog.csdnimg.cn/20191125160747517.png#pic_center)  
在前面的文章中对k3s进行了一些概要信息和安装选项以及离线安装方式的介绍，这篇文章通过具体的实例来介绍如何使用k3s搭建kubernetes集群。

集群环境准备
======

hostname

IP

内存

硬盘

操作系统

用途

host121

192.168.163.121

512MB

8G

CentOS 7.6

Master

host122

192.168.163.122

512MB

5G

CentOS 7.6

Node

host123

192.168.163.123

512MB

5G

CentOS 7.6

Node

host124

192.168.163.124

512MB

5G

CentOS 7.6

Node

注：本来Node节点想用256MB，最小化安装安装失败了，普通方式下，搭建kubernetes集群所用的节点，512M也只能算是非常低的配置了。

步骤1: 安装部署Master节点
=================

*   下载k3s二进制文件

> 执行命令：wget https://github.com/rancher/k3s/releases/download/v1.0.0/k3s

*   拷贝至/usr/local/bin下

> 执行命令：cp k3s /usr/local/bin/

*   设定执行权限

> 执行命令：chmod 755 /usr/local/bin/k3s

*   清空iptables规则

> 执行命令：iptables -F

*   确认版本

> 执行命令：k3s --version

执行日志如下所示：

    [root@host121 ~]# wget https://github.com/rancher/k3s/releases/download/v1.0.0/k3s
    ...省略
    [root@host121 ~]# cp k3s /usr/local/bin/
    [root@host121 ~]# chmod 755 /usr/local/bin/k3s
    [root@host121 ~]# iptables -F
    [root@host121 ~]# k3s --version
    k3s version v1.0.0 (18bd921c)
    [root@host121 ~]#
    

*   下载安装脚本

> 执行命令：curl https://raw.githubusercontent.com/rancher/k3s/master/install.sh -o install.sh

*   设定离线安装方式进行安装

> 执行命令：export INSTALL\_K3S\_SKIP\_DOWNLOAD=true && sh install.sh

*   确认kubernetes版本

> 执行命令：kubectl version

*   确认节点信息

> 执行命令：kubectl get node -o wide

执行日志如下所示：

    [root@host121 ~]# curl https://raw.githubusercontent.com/rancher/k3s/master/install.sh -o install.sh
      % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                     Dload  Upload   Total   Spent    Left  Speed
    100 20421  100 20421    0     0  23031      0 --:--:-- --:--:-- --:--:-- 23022
    [root@host121 ~]# export INSTALL_K3S_SKIP_DOWNLOAD=true && sh install.sh
    [INFO]  Skipping k3s download and verify
    which: no kubectl in (/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/root/bin)
    [INFO]  Creating /usr/local/bin/kubectl symlink to k3s
    which: no crictl in (/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/root/bin)
    [INFO]  Creating /usr/local/bin/crictl symlink to k3s
    which: no ctr in (/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/root/bin)
    [INFO]  Creating /usr/local/bin/ctr symlink to k3s
    [INFO]  Creating killall script /usr/local/bin/k3s-killall.sh
    [INFO]  Creating uninstall script /usr/local/bin/k3s-uninstall.sh
    [INFO]  env: Creating environment file /etc/systemd/system/k3s.service.env
    [INFO]  systemd: Creating service file /etc/systemd/system/k3s.service
    [INFO]  systemd: Enabling k3s unit
    Created symlink from /etc/systemd/system/multi-user.target.wants/k3s.service to /etc/systemd/system/k3s.service.
    [INFO]  systemd: Starting k3s
    [root@host121 ~]# kubectl version
    Client Version: version.Info{Major:"1", Minor:"16", GitVersion:"v1.16.3-k3s.2", GitCommit:"e7e6a3c4e9a7d80b87793612730d10a863a25980", GitTreeState:"clean", BuildDate:"2019-11-18T18:31:23Z", GoVersion:"go1.13.4", Compiler:"gc", Platform:"linux/amd64"}
    Server Version: version.Info{Major:"1", Minor:"16", GitVersion:"v1.16.3-k3s.2", GitCommit:"e7e6a3c4e9a7d80b87793612730d10a863a25980", GitTreeState:"clean", BuildDate:"2019-11-18T18:31:23Z", GoVersion:"go1.13.4", Compiler:"gc", Platform:"linux/amd64"}
    [root@host121 ~]# kubectl get node -o wide
    NAME      STATUS     ROLES    AGE   VERSION         INTERNAL-IP       EXTERNAL-IP   OS-IMAGE                KERNEL-VERSION          CONTAINER-RUNTIME
    host121   NotReady   master   5s    v1.16.3-k3s.2   192.168.163.121   <none>        CentOS Linux 7 (Core)   3.10.0-957.el7.x86_64   containerd://1.3.0-k3s.4
    [root@host121 ~]# 
    

cluster-info的详细信息

\[root@host121 ~\]# kubectl cluster-info  
Kubernetes master is running at https://127.0.0.1:6443  
CoreDNS is running at https://127.0.0.1:6443/api/v1/namespaces/kube-system/services/kube-dns:dns/proxy  
Metrics-server is running at https://127.0.0.1:6443/api/v1/namespaces/kube-system/services/https:metrics-server:/proxy

To further debug and diagnose cluster problems, use ‘kubectl cluster-info dump’.  
\[root@host121 ~\]#

步骤2: 安装设定节点
===========

*   事前准备  
    使用如下脚本进行事前准备，将k3s二进制文件拷贝至各节点的/usr/local/bin下，并设定执行权限

    for host in host122 host123 host124
    do
     echo "# Copy k3s to $host"
     scp k3s $host:/usr/local/bin/k3s
     ssh $host "chmod 755 /usr/local/bin/k3s"
     ssh $host "k3s --version"
     echo
    done
    

执行日志如下所示

    [root@host121 ~]# for host in host122 host123 host124
    > do
    > echo "# Copy k3s to $host"
    > scp k3s $host:/usr/local/bin/k3s
    > ssh $host "chmod 755 /usr/local/bin/k3s"
    > ssh $host "k3s --version"
    > echo
    > done
    # Copy k3s to host122
    k3s                                                                                                   100%   49MB  35.0MB/s   00:01    
    k3s version v1.0.0 (18bd921c)
    
    # Copy k3s to host123
    k3s                                                                                                   100%   49MB  34.6MB/s   00:01    
    k3s version v1.0.0 (18bd921c)
    
    # Copy k3s to host124
    k3s                                                                                                   100%   49MB  34.7MB/s   00:01    
    k3s version v1.0.0 (18bd921c)
    
    [root@host121 ~]#
    

拷贝安装脚本至各节点，执行日志如下所示：

    [root@host121 ~]# for host in host122 host123 host124; do  echo "# Copy install.sh to $host";  scp install.sh ${host}:/root/install.sh;  echo; done
    # Copy install.sh to host122
    install.sh                                                                                            100%   20KB 995.1KB/s   00:00    
    
    # Copy install.sh to host123
    install.sh                                                                                            100%   20KB   7.5MB/s   00:00    
    
    # Copy install.sh to host124
    install.sh                                                                                            100%   20KB   7.2MB/s   00:00    
    
    [root@host121 ~]# 
    

确认token信息

    [root@host121 ~]# cat /var/lib/rancher/k3s/server/node-token
    K10d7dd455049ee200cada3bf833b3157862a4d9ef07ab9785d3dc39f9b6a416fab::server:e55b636ebd8c962ae8721a8ab2ab0e4f
    [root@host121 ~]# 
    

安装非常简单，只需要正确设定K3S\_URL和K3S\_TOKEN然后执行安装脚本即可，以host122节点为例，执行日志如下所示：

    [root@host122 ~]# export K3S_URL=https://192.168.163.121:6443
    [root@host122 ~]# export K3S_TOKEN=K10d7dd455049ee200cada3bf833b3157862a4d9ef07ab9785d3dc39f9b6a416fab::server:e55b636ebd8c962ae8721a8ab2ab0e4f
    [root@host122 ~]# export INSTALL_K3S_SKIP_DOWNLOAD=true && sh install.sh agent
    [INFO]  Skipping k3s download and verify
    which: no kubectl in (/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/root/bin)
    [INFO]  Creating /usr/local/bin/kubectl symlink to k3s
    which: no crictl in (/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/root/bin)
    [INFO]  Creating /usr/local/bin/crictl symlink to k3s
    which: no ctr in (/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/root/bin)
    [INFO]  Creating /usr/local/bin/ctr symlink to k3s
    [INFO]  Creating killall script /usr/local/bin/k3s-killall.sh
    [INFO]  Creating uninstall script /usr/local/bin/k3s-agent-uninstall.sh
    [INFO]  env: Creating environment file /etc/systemd/system/k3s-agent.service.env
    [INFO]  systemd: Creating service file /etc/systemd/system/k3s-agent.service
    [INFO]  systemd: Enabling k3s-agent unit
    Created symlink from /etc/systemd/system/multi-user.target.wants/k3s-agent.service to /etc/systemd/system/k3s-agent.service.
    [INFO]  systemd: Starting k3s-agent
    [root@host122 ~]#
    

此时在master节点（host121）进行确认，可以得到如下信息

    [root@host121 ~]# kubectl get node -o wide
    NAME      STATUS   ROLES    AGE    VERSION         INTERNAL-IP       EXTERNAL-IP   OS-IMAGE                KERNEL-VERSION          CONTAINER-RUNTIME
    host121   Ready    master   6m9s   v1.16.3-k3s.2   192.168.163.121   <none>        CentOS Linux 7 (Core)   3.10.0-957.el7.x86_64   containerd://1.3.0-k3s.4
    host122   Ready    <none>   13s    v1.16.3-k3s.2   192.168.163.122   <none>        CentOS Linux 7 (Core)   3.10.0-957.el7.x86_64   containerd://1.3.0-k3s.4
    [root@host121 ~]# 
    

同样的操作在host123和host124执行之后，再次确认可以得到几乎实时的构成节点信息的变化

    [root@host121 ~]# kubectl get node -o wide
    NAME      STATUS   ROLES    AGE     VERSION         INTERNAL-IP       EXTERNAL-IP   OS-IMAGE                KERNEL-VERSION          CONTAINER-RUNTIME
    host121   Ready    master   7m16s   v1.16.3-k3s.2   192.168.163.121   <none>        CentOS Linux 7 (Core)   3.10.0-957.el7.x86_64   containerd://1.3.0-k3s.4
    host122   Ready    <none>   80s     v1.16.3-k3s.2   192.168.163.122   <none>        CentOS Linux 7 (Core)   3.10.0-957.el7.x86_64   containerd://1.3.0-k3s.4
    host123   Ready    <none>   6s      v1.16.3-k3s.2   192.168.163.123   <none>        CentOS Linux 7 (Core)   3.10.0-957.el7.x86_64   containerd://1.3.0-k3s.4
    host124   Ready    <none>   3s      v1.16.3-k3s.2   192.168.163.124   <none>        CentOS Linux 7 (Core)   3.10.0-957.el7.x86_64   containerd://1.3.0-k3s.4
    [root@host121 ~]# 
    

总结
==

从这篇文章可以看到，使用k3s无论是搭建单机版本还是搭建一个1主3从的集群都非常的简单，几分钟就可以完成，过程也几乎没有什么使用上的太多的大坑小坑，至少是学习k8s的必备神器。