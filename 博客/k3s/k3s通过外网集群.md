# [k3s通过外网集群](https://github.com/rancher/k3s/issues/1523)

## 方案1：
ran into the same issue. This is how I solved it

create cluster master node:

    export K3S_NODE_NAME=${HOSTNAME//_/-}
    export K3S_EXTERNAL_IP=xx.xx.xx.xx
    export INSTALL_K3S_EXEC="--docker --write-kubeconfig ~/.kube/config --write-kubeconfig-mode 666 --tls-san $K3S_EXTERNAL_IP --kube-apiserver-arg service-node-port-range=1-65000 --kube-apiserver-arg advertise-address=$K3S_EXTERNAL_IP --kube-apiserver-arg external-hostname=$K3S_EXTERNAL_IP"
    curl -sfL https://docs.rancher.cn/k3s/k3s-install.sh |  sh -
    
    

Get Token on master node:

    echo -e "export K3S_TOKEN=$(cat /var/lib/rancher/k3s/server/node-token)\nexport K3S_URL=https://$K3S_EXTERNAL_IP:6443\nexport INSTALL_K3S_EXEC=\"--docker --token \$K3S_TOKEN --server \$K3S_URL\""
    

join workers to the cluster:

    export K3S_TOKEN=xxxx
    export K3S_URL=https://xx.xx.xx.xx:6443
    export INSTALL_K3S_EXEC="--docker --token $K3S_TOKEN --server $K3S_URL"
    export K3S_NODE_NAME=${HOSTNAME//_/-}
    curl -sfL https://docs.rancher.cn/k3s/k3s-install.sh | sh -


----
## 方案2：

I am facing the same issue,  
On master node:

    curl -sfL https://get.k3s.io | INSTALL_K3S_EXEC="--write-kubeconfig ~/.kube/config --write-kubeconfig-mode 666 --tls-san <<public-ip>> --node-external-ip=<<public-ip>>" sh -

On agent node:  
    curl -sfL https://get.k3s.io | K3S_URL=https://<<public-ip>>:6443 K3S_TOKEN=mynodetoken sh -

----

经过试验发现方案2比较好用，方案1失败

目标机器都装上wireguard
server端加上如下参数：
--tls-san <public_ip> \
    --node-ip <public_ip> \
    --node-external-ip <public_ip> \
    --no-deploy servicelb \
    --flannel-backend wireguard \
    --kube-proxy-arg "proxy-mode=ipvs" "masquerade-all=true" \
    --kube-proxy-arg "metrics-bind-address=0.0.0.0"

将 <public_ip> 替换成控制节点的公网 IP 。
flannel 使用 wireguard 协议来跨主机通信。
kube-proxy 使用 ipvs 模式。

agent端都加上如下参数：
--node-external-ip <public_ip> \
    --node-ip <public_ip> \
    --kube-proxy-arg "proxy-mode=ipvs" "masquerade-all=true" \
    --kube-proxy-arg "metrics-bind-address=0.0.0.0"


其他问题具体还要参考：跨云厂商部署k3s集群.md