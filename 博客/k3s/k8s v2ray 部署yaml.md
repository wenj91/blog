# [k8s v2ray 部署yaml](https://gggitpl.com/2019/11/14/k8s-v2ray-%E9%83%A8%E7%BD%B2yaml/)
       

k8s v2ray 部署yaml
================

发表于 2019-11-14 更新于 2021-01-24

    root@4b ~# vim v2ray.yaml
    
    apiVersion: apps/v1
    kind: Deployment
    metadata:
      name: v2ray
    spec:
      replicas: 1
      selector:
        matchLabels:
          app: v2ray
      template:
        metadata:
          labels:
            app: v2ray
        spec:
          containers:
            - name: v2ray
              image: v2ray
              imagePullPolicy: IfNotPresent
              resources:
                limits:
                  memory: "128Mi"
                  cpu: "500m"
              ports:
                - containerPort: 1080
                - containerPort: 3128
              volumeMounts:
                - name: config
                  mountPath: /etc/v2ray/config.json
                  subPath: path/to/config.json
                - name: localtime
                  mountPath: /etc/localtime
              livenessProbe:
                tcpSocket:
                  port: 1080
                initialDelaySeconds: 5
                periodSeconds: 5
          volumes:
            - name: localtime
              hostPath:
                path: /etc/localtime
            - name: config
              configMap:
                name: v2ray
                items:
                  - key: config.json
                    path: path/to/config.json
    
    ---
    apiVersion: v1
    kind: Service
    metadata:
      name: v2ray
    spec:
      type: LoadBalancer
      selector:
        app: v2ray
      ports:
        - name: socks5
          port: 1080
          targetPort: 1080
        - name: http
          port: 3128
          targetPort: 3128
    
    ---
    apiVersion: v1
    kind: ConfigMap
    metadata:
      name: v2ray
    data:
      config.json: |
        {
          "log": {
            "loglevel": "debug"
          },
          "inbounds": [{
            "port": 1080,
            "listen": "0.0.0.0",
            "protocol": "socks",
            "sniffing": {
              "enabled": true,
              "destOverride": ["http", "tls"]
            },
            "settings": {
              "auth": "noauth",
              "udp": false
            }
          }, {
            "port": 3128,
            "listen": "0.0.0.0",
            "protocol": "http",
            "settings": {
              "timeout": 0
            }
          }],
          "outbounds": [{
            "protocol": "vmess",
            "settings": {
              "vnext": [{
                "address": "mydomain.me",
                "port": 443,
                "users": [{
                  "id": "1661759e-ae55-4188-b699-8dbfeca50576",
                  "alterId": 64
                }]
              }]
            },
            "streamSettings": {
              "network": "ws",
              "security": "tls",
              "wsSettings": {
                "path": "/ray"
              }
            }
          }]
        }

> 此为本地`v2ray`客户端部署, `imagePullPolicy: IfNotPresent` 如果本地不存在镜像到服务器拉取  
> 上面`configmap`中为本地客户端配置, 如果是在服务端请自行更改相应`image`与配置文件内容(可在博客中搜索`v2ray`)

[\# V2ray](https://gggitpl.com/tags/V2ray/)

[k8s部署nginx时配置文件挂载示例](/2019/11/13/k8s%E9%83%A8%E7%BD%B2nginx%E6%97%B6%E9%85%8D%E7%BD%AE%E6%96%87%E4%BB%B6%E6%8C%82%E8%BD%BD%E7%A4%BA%E4%BE%8B/ "k8s部署nginx时配置文件挂载示例")

[k8s adguard 部署yaml](/2019/11/14/k8s-adguard-%E9%83%A8%E7%BD%B2yaml/ "k8s adguard 部署yaml")