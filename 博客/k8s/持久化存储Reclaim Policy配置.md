# [持久化存储Reclaim Policy配置](https://blog.csdn.net/haiziccc/article/details/105220237)

最近需要部署一个MySQL容器，因为数据库存放的数据需要一直保存，就涉及到持久化的概念。容器本身是无状态的，出现问题后随时可销毁，但保存数据的的卷不能被销毁，需要创建一个持久卷。持久卷删除时有三种回售模式，

*   保持（Retain）:删除PV后后端存储上的数据仍然存在，如需彻底删除则需要手动删除后端存储volume
*   删除（Delete）：删除被PVC释放的PV和后端存储volume
*   回收（Recycle）：保留PV，但清空PV上的数据（已废弃）

默认情况下是delete模式，可以通过以下命令查看：

    获取所有持久卷# kubectl get pvNAME                                       CAPACITY   ACCESS MODES   RECLAIM POLICY   STATUS      CLAIM                                                           STORAGECLASS   REASON    AGEpvc-89ecc53b-725c-11ea-a8e2-005056aa2a6d   5Gi        RWX            Delete           Bound       service/mysql-pvc                                          isilon                   18h 查看持久卷具体信息# kubectl describe pv pvc-89ecc53b-725c-11ea-a8e2-005056aa2a6dName:            pvc-89ecc53b-725c-11ea-a8e2-005056aa2a6dLabels:          <none>Annotations:     pv.kubernetes.io/provisioned-by=isilonFinalizers:      [external-provisioner.volume.kubernetes.io/finalizer kubernetes.io/pv-protection]StorageClass:    isilonStatus:          BoundClaim:           service/mysql-pvcReclaim Policy:  DeleteAccess Modes:    RWXCapacity:        5GiNode Affinity:   <none>Message:Source:    Type:      NFS (an NFS mount that lasts the lifetime of a pod)    Server:    192.168.22.114    Path:      /ifs/app/service-mysql-pvc-pvc-89ecc53b-725c-11ea-a8e2-005056aa2a6d    ReadOnly:  falseEvents:        <none>

修改PVC reclaim policy

    #kubectl patch pv pvc-89ecc53b-725c-11ea-a8e2-005056aa2a6d -p '{"spec":{"persistentVolumeReclaimPolicy":"Retain"}}'