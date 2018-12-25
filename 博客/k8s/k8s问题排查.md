

## k8s hello, world例子
https://www.kubernetes.org.cn/2377.html

## 命令
kubectl get po -n kube-system
kubectl describe po -n kube-system kubernetes-dashboard
kubectl logs -n kube-system kubernetes-dashboard

kubectl -n kube-system delete pods $(kubectl -n kube-system get pod -o name | grep kube-dns)

kubectl delete --namespace=kube-system deployment kube-dns-autoscaler

kubectl -n kube-system get configmap coredns -oyaml

kubectl -n kube-system get pods -o wide

kube-dns-6f6cc86bf9-7htch

gcr.io/google_containers/cluster-autoscaler:v1.2.1


configmap
deployment
service


kubectl -n kube-system delete configmap coredns
kubectl -n kube-system delete deployment coredns
kubectl -n kube-system delete service kube-dns


nameserver 192.168.0.1

kubectl cluster-info

kubectl logs -n kube-system  kube-dns-2258483030-pd8qj -c kubedns
 kubectl logs -n kube-system  kube-dns-2258483030-pd8qj -c dnsmasq 
 kubectl logs -n kube-system  kube-dns-2258483030-pd8qj -c sidecar

 kubectl exec -it busybox -- ping 10.96.0.1