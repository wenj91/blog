# kubernetes安装dashboard v1.10.1

## 拉取镜像
docker pull mirrorgooglecontainers/kubernetes-dashboard-amd64:v1.10.1

## 重新打标签
docker tag mirrorgooglecontainers/kubernetes-dashboard-amd64:v1.10.1 k8s.gcr.io/kubernetes-dashboard-amd64:v1.10.1

## 删除无用镜像
docker rmi mirrorgooglecontainers/kubernetes-dashboard-amd64:v1.10.1

kubectl apply -f https://raw.githubusercontent.com/kubernetes/dashboard/v1.10.1/src/deploy/recommended/kubernetes-dashboard.yaml

查看dashboard的POD是否正常启动，如果正常说明安装成功
kubectl get pods --namespace=kube-system

--------------------- 
作者：三月泡 
来源：CSDN 
原文：https://blog.csdn.net/judyjie/article/details/85217617 
版权声明：本文为博主原创文章，转载请附上博文链接！