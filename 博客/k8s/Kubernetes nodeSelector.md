# [Kubernetes nodeSelector](https://www.jianshu.com/p/d376ae9d3edb)

Kubernetes nodeSelector
=======================

[![](https://upload.jianshu.io/users/upload_avatars/9505682/342a62e2-8a6d-4663-925b-6ea708495c32?imageMogr2/auto-orient/strip|imageView2/1/w/96/h/96/format/webp)](https://www.jianshu.com/u/98f47f8fd2c5)

[程序员同行者](https://www.jianshu.com/u/98f47f8fd2c5)关注

0.2722018.11.13 10:19:37字数 222阅读 14,106

labels 在 K8s 中是一个很重要的概念，作为一个标识，Service、Deployments 和 Pods 之间的关联都是通过 label 来实现的。而每个节点也都拥有 label，通过设置 label 相关的策略可以使得 pods 关联到对应 label 的节点上。

#### nodeSelector

`nodeSelector` 是最简单的约束方式。`nodeSelector` 是 `PodSpec` 的一个字段。

通过 `--show-labels` 可以查看当前 `nodes` 的`labels`

    $ kubectl get nodes --show-labels
    NAME       STATUS    ROLES     AGE       VERSION   LABELS
    minikube   Ready     <none>    1m        v1.10.0   beta.kubernetes.io/arch=amd64,beta.kubernetes.io/os=linux,kubernetes.io/
    hostname=minikube
    

如果没有额外添加 nodes labels，那么看到的如上所示的默认标签。我们可以通过 kubectl label node 命令给指定 node 添加 labels：

    $ kubectl label node minikube disktype=ssd
    node/minikube labeled
    $ kubectl get nodes --show-labels
    NAME       STATUS    ROLES     AGE       VERSION   LABELS
    minikube   Ready     <none>    5m        v1.10.0   beta.kubernetes.io/arch=amd64,beta.kubernetes.io/os=linux,disktype=ssd,kubernetes.io/host
    

当然，你也可以通过 kubectl label node 删除指定的 labels（标签 key 接 - 号即可）

    $ kubectl label node minikube disktype-
    node/minikube labeled
    $ kubectl get node --show-labels
    NAME       STATUS    ROLES     AGE       VERSION   LABELS
    minikube   Ready     <none>    23m       v1.10.0 beta.kubernetes.io/arch=amd64,beta.kubernetes.io/os=linux,kubernetes.io/hostname=minikube
    

创建测试 pod 并指定 nodeSelector 选项绑定节点：

    $ cat nginx.yaml
    apiVersion: v1
    kind: Pod
    metadata:
      name: nginx
      labels:
        env: test
    spec:
      containers:
      - name: nginx
        image: nginx
        imagePullPolicy: IfNotPresent
      nodeSelector:
        disktype: ssd
    $ kubectl create -f nginx.yaml
    pod/nginx created
    

查看 pod 调度的节点，即我们指定有 disktype=ssd label 的 minikube 节点：

    $ kubectl get pods -o wide
    NAME      READY     STATUS    RESTARTS   AGE       IP           NODE
    nginx     1/1       Running   0          1m        172.18.0.4   minikube
    

3人点赞

[Kubernetes](https://www.jianshu.com/nb/31331838)

"小礼物走一走，来简书关注我"

赞赏支持还没有人赞赏，支持一下

[![  ](https://upload.jianshu.io/users/upload_avatars/9505682/342a62e2-8a6d-4663-925b-6ea708495c32?imageMogr2/auto-orient/strip|imageView2/1/w/100/h/100/format/webp)](https://www.jianshu.com/u/98f47f8fd2c5)

[程序员同行者](https://www.jianshu.com/u/98f47f8fd2c5 "程序员同行者")Linux SA Linux Ops Python DevOps Go K8s ...

总资产23 (约2.22元)共写了9.1W字获得139个赞共86个粉丝

关注