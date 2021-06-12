# [轻量级k8s - k3s体验](https://nll.im/post/hello-k3s.html)


介绍
--

Rancher 开源 K3s：边缘计算场景下的轻量级 K8s 发行版  
https://www.infoq.cn/article/MIML5LiW5JMqs0PH-j89

对于一个中小型团队而言，搭建一套k8s集群并且维护，是一个比较有难度，成本较高的事情，虽然国内的云厂商也都推出自己的k8s平台，XKE，但是复杂度依然很高，k3s是一个简化的k8s发行版，显著降低了整体的复杂度，除了边缘计算的使用场景，K3s 还非常适合那些寻求简单方法来部署 Kubernetes 轻量级发行版的用户。

官网：[https://k3s.io/](https://k3s.io/)  
版本：k3s version v0.7.0 (61bdd852)

服务器: CentOS 7

安装
--

#### docker

实际k3s是不需要docker环境的，但是我们安装过程中有用到，所以需要先安装docker

sudo yum install epel-release -y
\# install docker
sudo yum remove docker docker-common docker-selinux docker-engine
sudo yum install -y yum-utils device-mapper-persistent-data lvm2 wget unzip zip lrzsz git hstr
\# 替换为国内镜像
wget -O /etc/yum.repos.d/docker-ce.repo https://download.docker.com/linux/centos/docker-ce.repo
sudo sed -i 's+download.docker.com+mirrors.tuna.tsinghua.edu.cn/docker-ce+' /etc/yum.repos.d/docker-ce.repo
sudo yum makecache fast
sudo yum install -y docker-ce
sudo systemctl enable docker
sudo systemctl start docker

#### k3s-server

curl -sfL https://nll-tools.pek3b.qingstor.com/download/latest/install.sh > install.sh
sudo mkdir -p /var/lib/rancher/k3s/agent/images/
curl -L -O https://nll-tools.pek3b.qingstor.com/download/latest/k3s-airgap-images-amd64.tar
sudo cp ./k3s-airgap-images-amd64.tar /var/lib/rancher/k3s/agent/images/
chmod u+x install.sh
INSTALL\_K3S\_SKIP\_START\=true ./install.sh
systemctl start k3s
\# look token
cat /var/lib/rancher/k3s/server/node-token

上面的命令中，安装脚本从官网的`curl -sfL https://get.k3s.io | sh -` 替换为国内镜像，shell中是从github下载相关程序，为了提高速度，所以放在了国内，包括以下文件，下载可以在github release下载。

    install.sh
    k3s-airgap-images-amd64.tar
    k3s
    sha256sum-amd64.txt

这里本来还有个k8s.gcr.io/pause:3.1 image在国内无法拉取的问题，但是通过k3s-airgap-images规避掉了，k3s-airgap-images本来是在纯内网，无外网情况下使用的。网上有提说使用docker tag的方式来解决pause无法pull的问题，但是我没有成功，就和下面ui中的image情况一样。

### k3s-agent

k3s-agent服务器不需要安装docker

\# install agent
curl -sfL https://nll-tools.pek3b.qingstor.com/download/latest/install.sh > install.sh
sudo mkdir -p /var/lib/rancher/k3s/agent/images/
curl -L -O https://nll-tools.pek3b.qingstor.com/download/latest/k3s-airgap-images-amd64.tar
sudo cp ./k3s-airgap-images-amd64.tar /var/lib/rancher/k3s/agent/images/
chmod u+x install.sh

\# 在k3s-server上执行，获取加入节点命令
echo K3S\_TOKEN\=\`cat /var/lib/rancher/k3s/server/node-token\` K3S\_URL\=https://\`hostname -I | awk '{print $1}'\`:6443 INSTALL\_K3S\_SKIP\_START\=true ./install.sh

\# 然后在agent服务器上执行输出的命令，然后再启动k3s-agent

systemctl start k3s-agent

### kubernetes-dashboard

docker pull mirrorgooglecontainers/kubernetes-dashboard-amd64:v1.10.1
docker tag mirrorgooglecontainers/kubernetes-dashboard-amd64:v1.10.1 k8s.gcr.io/kubernetes-dashboard-amd64:v1.10.1
curl -sfL https://raw.githubusercontent.com/kubernetes/dashboard/v1.10.1/src/deploy/recommended/kubernetes-dashboard.yaml > kubernetes-dashboard.yaml

修改`kubernetes-dashboard.yaml`中的image为`mirrorgooglecontainers/kubernetes-dashboard-amd64:v1.10.1` 网上很多教程是不修改的，我这里不修改不行。修改之后执行

kubectl apply -f kubernetes-dashboard.yaml

### 创建帐号

创建文件 `cluster-admin.yaml` 内容如下：

\# ------------------- Dashboard Service Account ------------------- #

apiVersion: v1
kind: ServiceAccount
metadata:
  name: admin-user
  namespace: kube-system

\---
\# ------------------- Dashboard ClusterRoleBinding ------------------- #

apiVersion: rbac.authorization.k8s.io/v1beta1
kind: ClusterRoleBinding
metadata:
  name: admin-user
roleRef:
  apiGroup: rbac.authorization.k8s.io
  kind: ClusterRole
  name: cluster-admin
subjects:
\- kind: ServiceAccount
  name: admin-user
  namespace: kube-system
  

然后执行

kubectl apply -f cluster-admin.yaml #创建成功后获取它的token
kubectl -n kube-system describe secret $(kubectl -n kube-system get secret | grep admin-user | awk '{print $1}')

上面的这些命令，网上有很多不同的版本，包括使用最新版2.x的kubernetes-dashboard，他的命名空间不是`kube-system` 而是`kubernetes-dashboard`，还有获取账户密钥的时候，也要找admin相关的，grep出来的可能有一堆别的。

启动kubectl proxy.

kubectl proxy --address\='0.0.0.0' --port\=8001 --accept-hosts\='.\*'

也可以用ssh做个代理(这里我直接用 master的地址访问ui，输入token无法跳转页面，所以用的代理)

更新： 这里无法用外部ip只能用localhost访问是因为kubectl proxy本身不应该使用外部访问，不安全。  
见 ： [可以通过NodePort的方式对外暴漏端口访问。](https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above>https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above#kubectl-proxy</a></span>
</p>


<p class=)

[](https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above>https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above#kubectl-proxy</a></span>
</p>


<p class=)

[参考：](https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above>https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above#kubectl-proxy</a></span>
</p>


<p class=) [在你自己电脑上执行ssh代理命令](https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above>https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above#nodeport</a></span>
</p>


<p class=)

[

ssh -L8001:localhost:8001 user@<ip-adress of the master>

这样就可以用本地地址访问

](https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above>https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above#nodeport</a></span>
</p>


<p class=)

[](https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above>https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above#nodeport</a></span>
</p>


<p class=)[http://localhost:8001/api/v1/namespaces/kube-system/services/https:kubernetes-dashboard:/proxy/](http://localhost:8001/api/v1/namespaces/kube-system/services/https:kubernetes-dashboard:/proxy/)

![](https://nll.im/_image/2019-08-07/k3s.jpg)

部署个nginx测试下

nginx.yaml

apiVersion: apps/v1
kind: Deployment
metadata:
  name: nginx-deployment
spec:
  selector:
    matchLabels:
      app: nginx
  replicas: 2
  template:
    metadata:
      labels:
        app: nginx
    spec:
      containers:
      \- name: nginx
        image: nginx:1.7.9
        ports:
        \- containerPort: 80

### rancher ui

rancher的ui还是更直观一点，测试下，在一台各个节点都能访问到的服务器（官方要求4G，2G也能跑）上执行

sudo docker run -d --restart\=unless-stopped -p 80:80 -p 443:443 rancher/rancher

等会儿之后，在浏览器访问 -> 设定密码和Rancher Server URL，Rancher Server URL填写的是Rancher所在的服务器ip，并不是k3s的master节点ip。 然后在界面上 `Add Cluster` -> 选择`Import` 输入Cluster Name，点击Create。

之后在k3s master节点上执行

curl --insecure -sfL https://${RANCHER\_SERVER\_URL}/v3/import/z6l844wgkhgvqx9kbqjfgfgtdzjgk2hnxscwfnstdm47xq7cm622mf.yaml | kubectl apply -f -

等待一会儿就导入成功了。

![](https://nll.im/_image/2019-08-07/rancher.jpg)

总结
--

总体搭建起来还是比较简单的，把相关资源准备好之后，基本上几分钟就可以搭建一个集群，在一些场景下体验会大大超过k8s。 不过我觉得阻碍大家使用k8s的，很多时候不是k8s本身，而是应用容器化，整个技术架构的调整，这才是比较头疼和需要投入的地方。

参考:

[k3s 安装小记](http://zxc0328.github.io/2019/06/04/k3s-setup/)  
[你的第一次轻量级K8S体验 —— 记一次Rancher 2.2 + K3S集成部署过程](https://blog.ilemonrain.com/docker/rancher-with-k3s.html)  
[kubernetes dashboard安装教程接上一章K8S安装](https://www.jianshu.com/p/5ff6e26d1912)  
[Cheap K3s Kubernetes Cluster with Dashboard UI](https://www.thebookofjoel.com/blog/cheap-production-k3s-with-dashboard-ui)  
[kubernetes Creating-sample-user](https://github.com/kubernetes/dashboard/wiki/Creating-sample-user)  
[k3s: Kubernetes Dashboard + load balancer](https://mindmelt.nl/mindmelt.nl/2019/04/08/k3s-kubernetes-dashboard-load-balancer/)  
[Meet K3s – A Lightweight Kubernetes Distribution for Raspberry Pi Cluster](http://collabnix.com/get-started-with-k3s-a-lightweight-kubernetes-distribution-for-raspberry-pi-cluster/)  
[https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above](https://github.com/kubernetes/dashboard/wiki/Accessing-Dashboard---1.7.X-and-above)

发表评论

 

![](https://nll.im/visitor.png)

撰写评论

*   
*   
*   

var people=\[ "fivesmallq" \] $(".new\_comment textarea").textcomplete(\[{ match: /@(\\S\*)$/, search: function(term, callback){ callback($.map(people, function(person){ return person.indexOf(term)==0 ? person : null })) }, replace: function(value){ if (value.indexOf(' ')!='-1'){ return '@' + value + ', '; } else{ return '@' + value + ' '; } }, index: 1 }\]);