# [CentOS 安装最新版的Wireguard](https://kotori.net/2018/10/21/centos-%e5%ae%89%e8%a3%85%e6%9c%80%e6%96%b0%e7%89%88%e7%9a%84wireguard/)

> 2017年，新一代VPN技术wireguard诞生。wireguard基于linux kernel内核运行，效率极高，速度很快，而且支持设备IP地址漫游功能，不仅适合服务器之间的互联，还适合在NAT环境下使用，包括家中的智能路由器，配合openwrt等路由器，可安装wireguard，实现路由器绑定wireguard代理功能。

其实我已经用Wireguard 很久了，但是一直没有时间去写如何配置，抽空写一篇博客来证明一下我自己还没死（

1\. Wireguard 服务器配置
-------------------

我用的是CentOS 7 64bit系统，而内核我使用的是 Linux 4.11.2-1.el7.elrepo 这个版本，因为这个版本是支持lotServer的，如果没有lotServer的授权，你也可以用nanqinglang 魔改版BBR。

首先先更新系统内核，我们执行以下命令

sudo yum update -y 
sudo rpm -ivh http://mirror.rc.usf.edu/compute\_lock/elrepo/kernel/el7/x86\_64/RPMS/kernel-ml-4.11.2-1.el7.elrepo.x86\_64.rpm --force
sudo rpm -ivh http://mirror.rc.usf.edu/compute\_lock/elrepo/kernel/el7/x86\_64/RPMS/kernel-ml-devel-4.11.2-1.el7.elrepo.x86\_64.rpm --force
sudo rpm -ivh http://mirror.rc.usf.edu/compute\_lock/elrepo/kernel/el7/x86\_64/RPMS/kernel-ml-headers-4.11.2-1.el7.elrepo.x86\_64.rpm --force

设置 grub 来使使用新内核默认启动

sudo grub2-set-default 0
sudo grub2-mkconfig

启动完毕后我们加入 Wireguard 的 yum 源

sudo curl -Lo /etc/yum.repos.d/wireguard.repo https://copr.fedorainfracloud.org/coprs/jdoss/wireguard/repo/epel-7/jdoss-wireguard-epel-7.repo
sudo yum install epel-release -y
sudo yum install wireguard-dkms wireguard-tools -y

记得开启IPv4的转发

echo "net.ipv4.ip\_forward = 1" >> /etc/sysctl.conf
sysctl -p

随后，使用命令创建Publickey和PrivateKey

mkdir /etc/wireguard
cd /etc/wireguard
wg genkey | tee privatekey | wg pubkey > publickey
chmod 777 -R /etc/wireguard
vim /etc/wireguard/wg0.conf

服务器端需要以下内容

\[Interface\]
Address = 10.0.0.1/24
ListenPort = 56660
PrivateKey = <Private Key>
PostUp = iptables -A FORWARD -i wg0 -j ACCEPT; iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
PostDown = iptables -D FORWARD -i wg0 -j ACCEPT; iptables -t nat -D POSTROUTING -o eth0 -j MASQUERADE
SaveConfig = true

PrivateKey则是你刚生成的PrivateKey，需要填入进去。PostUP和PostDown是开启和关闭时分别执行的命令，你需要根据需求自行修改。

创建服务器端的自动启动

systemctl enable wg-quick@wg0 

启动服务器端

wg-quick up wg0

至此，服务器端已经配置完毕，我们需要配置客户端

2.客户端配置
-------

安装过程与服务器一直，但是配置文件是不一样的，具体的需要看你的需求。

假设我们需要将两台服务器互联，以便访问其内网中设备。我们的配置将如下：

\[Interface\]
Address = 10.0.0.2/24
ListenPort = 56660
PrivateKey = <Private Key>
PostUp = bash /etc/route-add 
PostDown = bash /etc/route-del
SaveConfig = true
 
\[Peer\]
PublicKey = <服务器端的Public Key>
AllowedIPs = 10.0.0.1/32
Endpoint = 服务器端的公网IP:56660

然后，这边需要注意的是AllowedIPs  如果你写了0.0.0.0/0，你可能会被全部reroute，从而导致连不上服务器。因此我这边推荐你设置为两边的IP先测试完毕再调全局。

随后一样的，启动wireguard。

在服务器端设置以下内容

wg set wg0 peer <客户端的Public Key> allowed-ips 10.0.0.1/32

然后你会发现两个内网IP可以互通，

ping -c 10 10.0.0.1
PING 10.0.0.1 (10.0.0.1) 56(84) bytes of data.
64 bytes from 10.0.0.1: icmp\_seq=1 ttl=64 time=28.5 ms
64 bytes from 10.0.0.1: icmp\_seq=2 ttl=64 time=28.4 ms
64 bytes from 10.0.0.1: icmp\_seq=3 ttl=64 time=28.5 ms
64 bytes from 10.0.0.1: icmp\_seq=4 ttl=64 time=28.5 ms
64 bytes from 10.0.0.1: icmp\_seq=5 ttl=64 time=28.5 ms
64 bytes from 10.0.0.1: icmp\_seq=6 ttl=64 time=28.3 ms
64 bytes from 10.0.0.1: icmp\_seq=7 ttl=64 time=28.6 ms
64 bytes from 10.0.0.1: icmp\_seq=8 ttl=64 time=28.6 ms
64 bytes from 10.0.0.1: icmp\_seq=9 ttl=64 time=28.3 ms
64 bytes from 10.0.0.1: icmp\_seq=10 ttl=64 time=28.5 ms

--- 10.0.0.1 ping statistics ---
10 packets transmitted, 10 received, 0% packet loss, time 9012ms
rtt min/avg/max/mdev = 28.360/28.522/28.688/0.207 ms

那么，我们的wireguard就算是通了，现在要仔细来调整这个路由让他来符合我们的需求。

就拿刚刚所说，如果是为了访问互相的内网，你需要把内网IP加入到 AllowedIPs  里面，用逗号区分。

比如说如下

\[Interface\]
Address = 10.0.0.2/24
ListenPort = 56660
PrivateKey = <Private Key>
PostUp = bash /etc/route-add 
PostDown = bash /etc/route-del
SaveConfig = true
 
\[Peer\]
PublicKey = <服务器端的Public Key>
AllowedIPs = 10.0.0.1/32, 192.168.0.0/16
Endpoint = 服务器端的公网IP:56660

在你启动wireguard后，你能访问到服务器端的192.168.0.0/16这个段，哦当然，这种可以认为是对等互联，所以不存在服务器或者客户端这种说法。

而另外一种做法，是在路由器上部署的，实现翻墙功能，这种配置应该是这么写的

\[Interface\]
Address = 10.0.0.2/24
ListenPort = 56660
PrivateKey = <Private Key>
PostUp = bash /etc/route-add 
PostDown = bash /etc/route-del
SaveConfig = true
 
\[Peer\]
PublicKey = <服务器端的Public Key>
AllowedIPs = 0.0.0.0/0
Endpoint = 服务器端的公网IP:56660
PersistentKeepalive = 25

另外一点，你需要编辑一下 /etc/route-add 来确保你的服务器IP不走wireguard，否则可能会连不上。

启动后，默认会将你所有流量都通过wg0这个接口到你的服务器上，实现翻墙。

3\. Wireguard 的进阶玩法
-------------------

这个以后再写，涉及到我目前做的一个项目，主要是给客户做游戏加速用，据说他们效果还不错。

具体就是开了一台机器作为他们的接入点，然后我们通过专线将流量导入到日本端，实现全天候稳定的游戏加速服务。

![](https://kotori.net/wp-content/uploads/2018/10/BES7KPU6O08_CK4.png)

Wireguard只需要一个端口，只需要支持UDP并且内核支持，即可对接无数台VPS。

那么，聪明的人应该能想到一些东西了吧（这边先不公开了，等我玩腻了再说具体实现方案。

  

[南ことり の 小窝](https://kotori.net)原创文章，转载请注明来自：[CentOS 安装最新版的Wireguard](https://kotori.net/2018/10/21/centos-%e5%ae%89%e8%a3%85%e6%9c%80%e6%96%b0%e7%89%88%e7%9a%84wireguard/)