# k3s简单应用

> 上个月底， 由于一些迷之原因我从扇贝跑路了  
> 又过回了大学那会养生的日子，有空来捣鼓一些奇奇怪怪的东西。  
> 最近在研究**k3s**，最终目标是将自己的一些项目迁移上去  
> 这里开始准备写一系列的文章来记录一下整个过程

什么是k3s?
-------

> Lightweight Kubernetes. 5 less than k8s

先简单解释一下啥是**k8s**

> k8s是**容器编排系统**  
> 说的再直白一点：  
> k8s可以管理不同机子上的docker

现在各厂都在像**容器化**的方向发展

作为个人开发者当然也想用类似的工具

但k8s集群的部署要求太高了（因为要跑**etcd**）而不得不放弃

> 一个小型k8s集群master节点的推荐配置是 2c 8g ！！

  

今年2月26日Rancher发布了k3s 那时我就知道，有戏了！

k3s其实是**k8s**的一个**超轻量的发型版** 轻量到啥地步呢？

k3s打包后只有一个**40mb**大小的二进制包

运行**k3s server**只需要**200m**左右的ram

这样看来一台`1c1g的vps`就能愉快的跑起来了

我还发现有国外老哥用k3s组了一个**树莓派集群**

效果大概是这样的：

![](https://pic3.zhimg.com/v2-000869ba5d35ef32d010ba1f3ae05ebc_b.jpg)

![](https://pic3.zhimg.com/80/v2-000869ba5d35ef32d010ba1f3ae05ebc_1440w.jpg)

先跑起来
----

> 由于k3s目前不支持mac  
> 所以我找了一台**1c1g**的vultr家的vps来当master node  
> 如果你也想要买一台来练练手 这里有个推广链接 注册冲5刀可送50刀： [注册地址](https://link.zhihu.com/?target=https%3A//www.vultr.com/%3Fref%3D7914717-4F)

*   可以通过官网的**一键安装脚本**来安装

`curl -sfL https://get.k3s.io | sh -`

这样一个**单节点**的k3s服务就跑起来了

*   **在本机访问k3s集群**

**安装kubectl** ：`brew install kubectl`

**配置集群证书：**

将vps上的 `/etc/rancher/k3s/k3s.yaml` 的内容

写入本机的`~/.kube/config`文件

试一下： `kubectl get node`

![](https://pic2.zhimg.com/v2-f82ec2bba68f5cd40e8ccb85779f8715_b.jpg)

![](https://pic2.zhimg.com/80/v2-f82ec2bba68f5cd40e8ccb85779f8715_1440w.jpg)

**可以看到这台节点已经成功跑起来了(ready)**

再跑个应用
-----

一般都会跑个`helloworld`之类的app来试一下效果

我觉得这样太没意思啦，我们来跑个能用的比如 `v2ray`

### 起一个namepace

    kind: Namespace
    apiVersion: v1
    metadata:
      name: playground
      labels:
        name: playground

拷贝以上文件为`ns-playgroud.yml`

在文件对应的目录运行: `kubectl apply -f ns-playground.yml`

这样我们就有一个名为`playground`的namespce了

运行 `kubectl get ns` 可以查看所有的namespace

![](https://pic4.zhimg.com/v2-717f164852ae0d7cb4499ff1d8b3b054_b.jpg)

![](https://pic4.zhimg.com/80/v2-717f164852ae0d7cb4499ff1d8b3b054_1440w.jpg)

  

### 起一个`configmap` 来存v2ray的配置文件

    apiVersion: v1
    kind: ConfigMap
    metadata:
      name: v2ray-config-file
      namespace: playground
      labels:
        k8s-app: v2ray
    data:
      config.json: |-
        {
            "stats": {},
            "api": {
                "tag": "api",
                "services": [
                    "StatsService"
                ]
            },
            "policy": {
                "levels": {
                    "0": {
                        "statsUserUplink": true,
                        "statsUserDownlink": true
                    }
                },
                "system": {
                    "statsInboundUplink": true,
                    "statsInboundDownlink": true
                }
            },
            "log": {
                "access": "/dev/stdout",
                "error": "/dev/stderr",
                "loglevel": "info"
            },
            "inbound": {
                "port": 8002,
                "tag": "statin",
                "protocol": "vmess",
                "settings": {
                    "clients": [
                        {
                            "id": "11111111-2222-3333-4444-555555555555",
                            "level": 1,
                            "alterId": 16,
                            "email": "ehco@ss.com"
                        }
                    ]
                }
            },
            "inboundDetour": [
              {
                "listen": "127.0.0.1",
                "port": 6000,
                "protocol": "dokodemo-door",
                "settings": {
                  "address": "127.0.0.1"
                },
                "tag": "api"
              }
            ],
            "outbound": {
                "protocol": "freedom",
                "settings": {}
            },
            "routing": {
                "strategy": "rules",
                "settings": {
                    "rules": [
                        {
                            "inboundTag": [
                                "api"
                            ],
                            "outboundTag": "api",
                            "type": "field"
                        }
                    ]
                }
            }
        }

拷贝以上文件为`cm-v2ray.yml`

在文件对应的目录运行: `kubectl apply -f cm-v2ray.yml -n playground`

这样v2ray的配置文件就作为一种**资源被存在k3s里了**

### 起一个deployment来跑v2ray

    apiVersion: extensions/v1beta1
    kind: Deployment
    metadata:
      name: v2ray-vu
      namespace: playground
    spec:
      replicas: 1
      selector:
        matchLabels:
          app: v2ray-vu
      strategy:
        rollingUpdate:
          maxSurge: 25%
          maxUnavailable: 25%
        type: RollingUpdate
      template:
        metadata:
          labels:
            app: v2ray-vu
        spec:
          containers:
          - name: app
            image: v2ray/official
            imagePullPolicy: IfNotPresent
            ports:
            - containerPort: 8002
            resources:
              limits:
                cpu: 200m
                memory: 200Mi
              requests:
                cpu: 100m
                memory: 100Mi
            volumeMounts:
              - name: v2ray-config-file
                mountPath: "/etc/v2ray"
                readOnly: true
          volumes:
          - name: v2ray-config-file
            configMap:
              name: v2ray-config-file

这里用了我们刚创建的configmap ：`v2ray-config-file`

并将其挂载到容器的`/etc/v2ray`目录下

这样v2ray跑起来的时候就能读取到准备的配置了

拷贝以上文件为`dp-v2ray.yml`

在文件对应的目录运行: `kubectl apply -f dp-v2ray.yml -n playground playground`

看一下服务有没有跑起来: `kubectl get pod -n playground`

![](https://pic3.zhimg.com/v2-74628cafc1be89d6a6cd8ae566860d5d_b.jpg)

![](https://pic3.zhimg.com/80/v2-74628cafc1be89d6a6cd8ae566860d5d_1440w.jpg)

可以看到v2ray已经处于`running`状态了

### 起一个service来暴露v2ray的服务

    kind: Service
    apiVersion: v1
    metadata:
      name: v2ray-vu
      namespace: playground
    spec:
      selector:
        app: v2ray-vu
      ports:
      - port: 8002
        targetPort: 8002
      externalIPs:
        - xx.xx.xx.xx

注意配置里的`externalIPs` 需要替换成你**自己vps的ip**

拷贝以上文件为`svc-v2ray.yml`

在文件对应的目录运行: `kubectl apply -f svc-v2ray.yml -n playground playground`

这样我们就将v2ray的服务暴露在节点的**8002**端口

所有从8002端口流量都会被转发到v2ray的**pod**里

### 看一下效果

查看访问日志 ： `kubectl logs -f podname -n playground`

podname 可以通过之前的`get pod` 命令获得

![](https://pic2.zhimg.com/v2-d76b74510a10bf71143768c0160980e0_b.jpg)

![](https://pic2.zhimg.com/80/v2-d76b74510a10bf71143768c0160980e0_1440w.jpg)

  

测个速度看看：

![](https://pic1.zhimg.com/v2-cc66e25c9143ce3db6fccdd3e486b15c_b.jpg)

![](https://pic1.zhimg.com/80/v2-cc66e25c9143ce3db6fccdd3e486b15c_1440w.jpg)

  

**效果不错，打完收工！**

最后
--

这篇文章还是会发在爬虫的专栏下

如果这篇文章反响好的话 我可能会再开一个`服务器/云`相关的专栏

哪位大佬给取个名字呗？