k8s之RBAC授权模式
============

**导读**
------

上一篇说了k8s的授权管理，这一篇就来详细看一下RBAC授权模式的使用

**RBAC授权模式**
------------

基于角色的访问控制，启用此模式，需要在API Server的启动参数上添加如下配置，（k8s默然采用此授权模式）。

    --authorization-mode=RBAC

  

![](https://pic2.zhimg.com/v2-b8d477bf21a2806c8b6fece9863dcd0d_b.jpg)

![](https://pic2.zhimg.com/80/v2-b8d477bf21a2806c8b6fece9863dcd0d_720w.jpg)

  

/etc/kubernetes/manifests/kube-apiserver.yaml

（1）对集群中的资源及非资源权限均有完整的覆盖

（2）整个RBAC完全由几个API对象完成，同其他API对象一样，可以用kubelet或API进行操作。

（3）可在运行时进行调整，无须重启API Server

**1 RBAC资源对象说明**
----------------

RBAC有四个资源对象，分别是Role、ClusterRole、RoleBinding、ClusterRoleBinding

**1.1 Role：角色**
---------------

一组权限的集合，在一个命名空间中，可以用其来定义一个角色，只能对命名空间内的资源进行授权。如果是集群级别的资源，则需要使用ClusterRole。例如：定义一个角色用来读取Pod的权限

    apiVersion: rbac.authorization.k8s.io/v1
    kind: Role
    metadata:
      namespace: rbac
      name: pod-read
    rules:
    - apiGroups: [""]
      resources: ["pods"]
      resourceNames: []
      verbs: ["get","watch","list"]

rules中的参数说明：

1、apiGroups：支持的API组列表，例如："apiVersion: batch/v1"等

2、resources：支持的资源对象列表，例如pods、deplayments、jobs等

3、resourceNames: 指定resource的名称

3、verbs：对资源对象的操作方法列表。

创建后查看：

  

![](https://pic3.zhimg.com/v2-2da5635f8b27694598f0106c593545fa_b.jpg)

![](https://pic3.zhimg.com/80/v2-2da5635f8b27694598f0106c593545fa_720w.jpg)

  

**1.2 ClusterRole：集群角色**
------------------------

具有和角色一致的命名空间资源的管理能力，还可用于以下特殊元素的授权

1、集群范围的资源，例如Node

2、非资源型的路径，例如：/healthz

3、包含全部命名空间的资源，例如Pods

例如：定义一个集群角色可让用户访问任意secrets

    apiVersion: rbac.authorization.k8s.io/v1
    kind: ClusterRole
    metadata:
      name: secrets-clusterrole
    rules:
    - apiGroups: [""]
      resources: ["secrets"]
      verbs: ["get","watch","list"]

**1.3 RoleBinding：角色绑定，ClusterRoleBinding：集群角色绑定**
--------------------------------------------------

角色绑定和集群角色绑定用于把一个角色绑定在一个目标上，可以是User，Group，Service Account，使用RoleBinding为某个命名空间授权，使用ClusterRoleBinding为集群范围内授权。

例如：将在rbac命名空间中把pod-read角色授予用户es

    apiVersion: rbac.authorization.k8s.io/v1
    kind: RoleBinding
    metadata:
      name: pod-read-bind
      namespace: rbac
    subjects:
    - kind: User
      name: es
      apiGroup: rbac.authorization.k8s.io
    roleRef:
    - kind: Role
      name: pod-read
      apiGroup: rbac.authorizatioin.k8s.io

创建之后切换到es用户，看能否查看相应Pod的资源

  

![](https://pic3.zhimg.com/v2-abcb8420fb5f4a0bf4f98044288f0fa2_b.jpg)

![](https://pic3.zhimg.com/80/v2-abcb8420fb5f4a0bf4f98044288f0fa2_720w.jpg)

  

RoleBinding也可以引用ClusterRole，对属于同一命名空间内的ClusterRole定义的资源主体进行授权， 例如：es能获取到集群中所有的资源信息

    apiVersion: rbac.authorization.k8s.io/v1
    kind: RoleBinding
    metadata:
      name: es-allresource
      namespace: rbac
    subjects:
    - kind: User
      name: es
      apiGroup: rbac.authorization.k8s.io
    roleRef:
      apiGroup: rbac.authorization.k8s.io
      kind: ClusterRole
      name: cluster-admin 

创建之后查看：

  

![](https://pic1.zhimg.com/v2-0335dbe6863647cea0bebe27118e938c_b.jpg)

![](https://pic1.zhimg.com/80/v2-0335dbe6863647cea0bebe27118e938c_720w.jpg)

  

集群角色绑定的角色只能是集群角色，用于进行集群级别或对所有命名空间都生效的授权

例如：允许manager组的用户读取所有namaspace的secrets

    apiVersion: rabc.authorization.k8s.io/v1
    kind: ClusterRoleBinding
    metadata:
      name: read-secret-global
    subjects:
    - kind: Group
      name: manager
      apiGroup: rabc.authorization.k8s.io
    ruleRef:
    - kind: ClusterRole
      name: secret-read
      apiGroup: rabc.authorization.k8s.io

**2 资源的引用方式**
-------------

多数资源可以用其名称的字符串表示，也就是Endpoint中的URL相对路径，例如pod中的日志是GET /api/v1/namaspaces/{namespace}/pods/{podname}/log

如果需要在一个RBAC对象中体现上下级资源，就需要使用“/”分割资源和下级资源。

例如：若想授权让某个主体同时能够读取Pod和Pod log，则可以配置 resources为一个数组

    apiVersion: rabc.authorization.k8s.io/v1
    kind: Role
    metadata: 
      name: logs-reader
      namespace: default
    rules:
    - apiGroups: [""]
      resources: ["pods","pods/log"]
      verbs: ["get","list"]

资源还可以通过名称（ResourceName）进行引用，在指定ResourceName后，使用get、delete、update、patch请求，就会被限制在这个资源实例范围内

例如，下面的声明让一个主体只能对名为my-configmap的ConFigmap进行get和update操作：

    apiVersion: rabc.authorization.k8s.io/v1
    kind: Role
    metadata:
      namaspace: default
      name: configmap-update
    rules:
    - apiGroups: [""]
      resources: ["configmap"]
      resourceNames: ["my-configmap"]
      verbs: ["get","update"]

**3 常见角色示例**
------------

**（1）允许读取核心API组的Pod资源**
-----------------------

    rules:
    - apiGroups: [""]
      resources: ["pods"]
      verbs: ["get","list","watch"]

**（2）允许读写extensions和apps两个API组中的deployment资源**
----------------------------------------------

    rules:
    - apiGroups: ["extensions","apps"]
      resources: ["deployments"]
      verbs: ["get","list","watch","create","update","patch","delete"]

**（3）允许读取Pod以及读写job信息**
-----------------------

    rules:
    - apiGroups: [""]
      resources: ["pods"]
      verbs: ["get","list","watch"]、
    - apiVersion: ["batch","extensions"]
      resources: ["jobs"]
      verbs: ["get","list","watch","create","update","patch","delete"]

**（4）允许读取一个名为my-config的ConfigMap（必须绑定到一个RoleBinding来限制到一个Namespace下的ConfigMap）：**
---------------------------------------------------------------------------------

    rules:
    - apiGroups: [""]
      resources: ["configmap"]
      resourceNames: ["my-configmap"]
      verbs: ["get"]

**（5）读取核心组的Node资源（Node属于集群级的资源，所以必须存在于ClusterRole中，并使用ClusterRoleBinding进行绑定）：**
--------------------------------------------------------------------------------

    rules:
    - apiGroups: [""]
      resources: ["nodes"]
      verbs: ["get","list","watch"]

**（6）允许对非资源端点“/healthz”及其所有子路径进行GET和POST操作（必须使用ClusterRole和ClusterRoleBinding）：**
---------------------------------------------------------------------------------

    rules:
    - nonResourceURLs: ["/healthz","/healthz/*"]
      verbs: ["get","post"]

**4 常见的角色绑定示例**
---------------

**（1）用户名alice**
---------------

    subjects:
    - kind: User
      name: alice
      apiGroup: rbac.authorization.k8s.io

**（2）组名alice**
--------------

    subjects:
    - kind: Group
      name: alice
      apiGroup: rbac.authorization.k8s.io

**（3）kube-system命名空间中默认Service Account**
----------------------------------------

    subjects:
    - kind: ServiceAccount
      name: default
      namespace: kube-system

**（4）qa命名空间中的所有Service Account：**
---------------------------------

    subjects:
    - kind: Group
      name: systeml:serviceaccounts:qa
      apiGroup: rbac.authorization.k8s.io

**（5）所有Service Account**
------------------------

    subjects:
    - kind: Group
      name: system:serviceaccounts
      apiGroup: rbac.authorization.k8s.io

**（6）所有认证用户**
-------------

    subjects:
    - kind: Group
      name: system:authenticated
      apiGroup: rbac.authorization.k8s.io

**（7）所有未认证用户**
--------------

    subjects:
    - kind: Group
      name: system:unauthenticated
      apiGroup: rbac.authorization.k8s.io

**（8）全部用户**
-----------

    subjects:
    - kind: Group
      name: system:authenticated
      apiGroup: rbac.authorization.k8s.io
    - kind: Group
      name: system:unauthenticated
      apiGroup: rbac.authorization.k8s.io

**5 默认的角色和角色绑定**
----------------

API Server会创建一套默认的ClusterRole和ClusterRoleBinding对象，其中很多是以“system:”为前缀的，以表明这些资源属于基础架构，对这些对象的改动可能造成集群故障。所有默认的ClusterRole和RoleBinding都会用标签[http://kubernetes.io/boostrapping=rbac-default](https://link.zhihu.com/?target=http%3A//kubernetes.io/boostrapping%3Drbac-default)进行标记。

**6 对Service Account的授权管理**
---------------------------

Service Account也是一种账号，是给运行在Pod里的进程提供了必要的身份证明。需要在Pod定义中指明引用的Service Account，这样就可以对Pod的进行赋权操作。例如：pod内可获取rbac命名空间的所有Pod资源，pod-reader-sc的Service Account是绑定了名为pod-read的Role

    apiVersion: v1
    kind: Pod
    metadata:
      name: nginx
      namespace: rbac
    spec:
      serviceAccountName: pod-reader-sc
      containers:
      - name: nginx
        image: nginx
        imagePullPolicy: IfNotPresent
        ports:
        - containerPort: 80

默认的RBAC策略为控制平台组件、节点和控制器授予有限范围的权限，但是除kube-system外的Service Account是没有任何权限的。

**（1）为一个应用专属的Service Account赋权**
--------------------------------

此应用需要在Pod的spec中指定一个serviceAccountName，用于API、Application Manifest、kubectl create serviceaccount等创建Service Account的命令。

例如为my-namespace中的my-sa Service Account授予只读权限

    kubectl create rolebinding my-sa-view --clusterrole=view --serviceaccount=my-namespace:my-sa --namespace=my-namespace

**（2）为一个命名空间中名为default的Service Account授权**
------------------------------------------

如果一个应用没有指定 serviceAccountName，则会使用名为default的Service Account。注意，赋予Service Account “default”的权限会让所有没有指定serviceAccountName的Pod都具有这些权限

例如，在my-namespace命名空间中为Service Account“default”授予只读权限：

    kubectl create rolebinding default-view --clusterrole=view --serviceaccount=my-namespace:default --namespace=my-namespace

另外，许多系统级Add-Ons都需要在kube-system命名空间中运行，要让这些Add-Ons能够使用超级用户权限，则可以把cluster-admin权限赋予kube-system命名空间中名为default的Service Account，这一操 作意味着kube-system命名空间包含了通向API超级用户的捷径。

    kubectl create clusterrolebinding add-ons-add-admin --clusterrole=cluster-admin --serviceaccount=kube-system:default

**（3）为命名空间中所有Service Account都授予一个角色**
-------------------------------------

如果希望在一个命名空间中，任何Service Account应用都具有一个角色，则可以为这一命名空间的Service Account群组进行授权

    kubectl create rolebinding serviceaccounts-view --clusterrole=view --group=system:serviceaccounts:my-namespace --namespace=my-namespace

**（4）为集群范围内所有Service Account都授予一个低权限角色**
----------------------------------------

如果不想为每个命名空间管理授权，则可以把一个集群级别的角色赋给所有Service Account。

    kubectl create clusterrolebinding serviceaccounts-view --clusterrole=view --group=system:serviceaccounts

**（5）为所有Service Account授予超级用户权限**
---------------------------------

    kubectl create clusterrolebinding serviceaccounts-view --clusterrole=cluster-admin --group=system:serviceaccounts

**7 使用kubectl命令行工具创建资源对象**
--------------------------

**（1）在命名空间rbac中为用户es授权admin ClusterRole：**
------------------------------------------

    kubectl create rolebinding bob-admin-binding --clusterrole=admin --user=es --namespace=rbac

**（2）在命名空间rbac中为名为myapp的Service Account授予view ClusterRole：**
------------------------------------------------------------

    kubctl create rolebinding myapp-role-binding --clusterrole=view --serviceaccount=acme:myapp --namespace=rbac

**（3）在全集群范围内为用户root授予cluster-admin ClusterRole：**
-------------------------------------------------

    kubectl create clusterrolebinding cluster-binding --clusterrole=cluster-admin --user=root

**（4）在全集群范围内为名为myapp的Service Account授予view ClusterRole：**
---------------------------------------------------------

    kubectl create clusterrolebinding service-account-binding --clusterrole=view --serviceaccount=acme:myapp

\===============================

我是Liusy，一个喜欢健身的程序员。

欢迎关注微信公众号【上古伪神】，一起交流Java技术及健身，获取更多干货，领取Java进阶干货，领取最新大厂面试资料，一起成为Java大神。

来都来了，关注一波再溜呗。
