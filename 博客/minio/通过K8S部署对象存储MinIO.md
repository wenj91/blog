# [通过K8S部署对象存储MinIO](https://www.jianshu.com/p/2d45990dd652)

通过K8S部署对象存储MinIO
================

[![](https://upload.jianshu.io/users/upload_avatars/10839544/f0b65806-b9b0-432b-84d9-f328ccbed549?imageMogr2/auto-orient/strip|imageView2/1/w/96/h/96/format/webp)](https://www.jianshu.com/u/aa3d5c41012b)

[sjyu\_eadd](https://www.jianshu.com/u/aa3d5c41012b)关注

22020.04.15 16:52:48字数 406阅读 1,375

MinIO 是全球领先的对象存储先锋，以 Apache License v2.0 发布的对象存储服务器，是为云应用和虚拟机而设计的分布式对象存储服务器。在标准硬件上，读/写速度上高达183GB/s和171GB/s。它与 Amazon S3 云存储服务兼容。 它最适用于存储非结构化数据，如照片、视频、日志文件、备份和容器/虚拟机映像。 对象的大小可以从几KB 到最大5TB。

*   对象存储，兼容Amazon S3协议
*   安装运维相对简单，开箱即用
*   后端除了本地文件系统，还支持多种存储系统，目前已经包括 OSS
*   原生支持bucket事件通知机制
*   可通过多节点集群方式，支持一定的高可用和数据容灾
*   有WEB管理界面和CLI，可以用于测试或者管理
*   公开bucket中的数据可以直接通过HTTP获取

MinIO是一个非常轻量的服务,可以很简单的和其他应用的结合，类似 NodeJS, Redis 或者 MySQL。

MinIO支持多种灵活的部署方式，支持Docker Compose、Docker Swam、Kubernetes等，详见官网：[https://docs.min.io/docs/minio-deployment-quickstart-guide.html](https://links.jianshu.com/go?to=https%3A%2F%2Fdocs.min.io%2Fdocs%2Fminio-deployment-quickstart-guide.html)或者[https://min.io/download#/linux](https://links.jianshu.com/go?to=https%3A%2F%2Fmin.io%2Fdownload%23%2Flinux)

这里着重介绍K8S下部署

1、standalone模式

    apiVersion: v1
    kind: PersistentVolume
    metadata:
      labels:
        app: minio
        release: minio
      name: minio
      namespace: default
    spec:
      accessModes:
      - ReadWriteOnce
      capacity:
        storage: 10Gi
      volumeMode: Filesystem
      hostPath:
        path: /mnt/minio
    ---
    apiVersion: v1
    kind: PersistentVolumeClaim
    metadata:
      # This name uniquely identifies the PVC. Will be used in deployment below.
      name: minio-pv-claim
      labels:
        app: minio-storage-claim
    spec:
      # Read more about access modes here: https://kubernetes.io/docs/user-guide/persistent-volumes/#access-modes
      accessModes:
        - ReadWriteOnce
      resources:
        # This is the request for storage. Should be available in the cluster.
        requests:
          storage: 10Gi
      # Uncomment and add storageClass specific to your requirements below. Read more https://kubernetes.io/docs/concepts/storage/persistent-volumes/#class-1
      #storageClassName:
    ---
    apiVersion: apps/v1
    kind: Deployment
    metadata:
      # This name uniquely identifies the Deployment
      name: minio-deployment
    spec:
      strategy:
        type: Recreate
      selector:
        matchLabels:
          app: minio
      template:
        metadata:
          labels:
            # Label is used as selector in the service.
            app: minio
        spec:
          # Refer to the PVC created earlier
          volumes:
          - name: storage
            persistentVolumeClaim:
              # Name of the PVC created earlier
              claimName: minio-pv-claim
          containers:
          - name: minio
            # Pulls the default MinIO image from Docker Hub
            image: minio/minio
            args:
            - server
            - /storage
            env:
            # MinIO access key and secret key
            - name: MINIO_ACCESS_KEY
              value: "admin123"
            - name: MINIO_SECRET_KEY
              value: "admin123"
            ports:
            - containerPort: 9000
            # Mount the volume into the pod
            volumeMounts:
            - name: storage # must match the volume name, above
              mountPath: "/storage"
    ---
    apiVersion: v1
    kind: Service
    metadata:
      name: minio-service
    spec:
      type: NodePort
      ports:
        - port: 9000
          targetPort: 9000
          protocol: TCP
      selector:
        app: minio
    

![](https://upload-images.jianshu.io/upload_images/10839544-9fd75c4c0273c0fb.png?imageMogr2/auto-orient/strip|imageView2/2/w/1200/format/webp)

image.png

  

由于service采用NodePort类型，通过主机IP:32593访问web

  

![](https://upload-images.jianshu.io/upload_images/10839544-b40df33694adbb37.png?imageMogr2/auto-orient/strip|imageView2/2/w/922/format/webp)

image.png

  

![](https://upload-images.jianshu.io/upload_images/10839544-4eeecd21b003380b.png?imageMogr2/auto-orient/strip|imageView2/2/w/1200/format/webp)

image.png

2、distributed模式

    apiVersion: v1
    kind: Service
    metadata:
      name: minio
      labels:
        app: minio
    spec:
      clusterIP: None
      ports:
        - port: 9000
          name: minio
      selector:
        app: minio
    ---
    apiVersion: apps/v1
    kind: StatefulSet
    metadata:
      name: minio
    spec:
      serviceName: minio
      replicas: 4
      selector:
        matchLabels:
          app: minio
      template:
        metadata:
          labels:
            app: minio
        spec:
          containers:
          - name: minio
            env:
            - name: MINIO_ACCESS_KEY
              value: "admin123"
            - name: MINIO_SECRET_KEY
              value: "admin123"
            image: minio/minio
            args:
            - server
            - http://minio-{0...3}.minio.default.svc.cluster.local/data
            ports:
            - containerPort: 9000
            # These volume mounts are persistent. Each pod in the PetSet
            # gets a volume mounted based on this field.
            volumeMounts:
            - name: data
              mountPath: /data
      # These are converted to volume claims by the controller
      # and mounted at the paths mentioned above.
      volumeClaimTemplates:
      - metadata:
          name: data
        spec:
          accessModes:
            - ReadWriteOnce
          resources:
            requests:
              storage: 10Gi
          # Uncomment and add storageClass specific to your requirements below. Read more https://kubernetes.io/docs/concepts/storage/persistent-volumes/#class-1
          #storageClassName:
    ---
    apiVersion: v1
    kind: Service
    metadata:
      name: minio-service
    spec:
      type: NodePort
      ports:
        - port: 9000
          targetPort: 9000
          protocol: TCP
      selector:
        app: minio
    

分布式部署，实例数至少4个，所以需要另外创建4个pv

![](https://upload-images.jianshu.io/upload_images/10839544-343c9eb448924549.png?imageMogr2/auto-orient/strip|imageView2/2/w/1029/format/webp)

image.png

image.png

13人点赞

[日记本](https://www.jianshu.com/nb/22683888)

"如果觉得我的文章对你有帮助，请随意赞赏。您的支持将鼓励我继续创作！"

赞赏支持还没有人赞赏，支持一下

[![  ](https://upload.jianshu.io/users/upload_avatars/10839544/f0b65806-b9b0-432b-84d9-f328ccbed549?imageMogr2/auto-orient/strip|imageView2/1/w/100/h/100/format/webp)](https://www.jianshu.com/u/aa3d5c41012b)

[sjyu\_eadd](https://www.jianshu.com/u/aa3d5c41012b "sjyu_eadd")关注云计算、边缘计算、大数据、AI等技术

总资产1 (约0.15元)共写了4.6W字获得103个赞共77个粉丝

关注