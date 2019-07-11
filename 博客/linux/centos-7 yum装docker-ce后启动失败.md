# [centos-7 yum装docker-ce后启动失败](https://www.cnblogs.com/FoChen/p/8708932.html)
相关版本：

centos-7:   CentOS Linux release 7.0.1406 (Core)

docker-ce: Docker version 18.03.0-ce, build 0520e24

yum docker 镜像：

http://mirrors.aliyun.com/docker-ce/linux/centos/docker-ce.repo

概要过程：

安装成功，启动报错
====================================

[root@Docker ~]# systemctl start docker
Job for docker.service failed because the control process exited with error code. See "systemctl status docker.service" and "journalctl -xe" for details.
-------------------------------------------------

查看系统日志
====================================

Apr 3 15:31:11 Docker systemd: Starting Docker Application Container Engine...
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11.659260747+08:00" level=info msg="libcontainerd: started new docker-containerd process" pid=5156
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="starting containerd" module=containerd revision=cfd04396dc68220d1cecbe686a6cc3aa5ce3667c version=v1.0.2
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.content.v1.content"..." module=containerd type=io.containerd.content.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.snapshotter.v1.btrfs"..." module=containerd type=io.containerd.snapshotter.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=warning msg="failed to load plugin io.containerd.snapshotter.v1.btrfs" error="path /var/lib/docker/containerd/daemon/io.containerd.snapshotter.v1.btrfs must be a btrfs filesystem to be used with the btrfs snapshotter" module=containerd
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.snapshotter.v1.overlayfs"..." module=containerd type=io.containerd.snapshotter.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=warning msg="failed to load plugin io.containerd.snapshotter.v1.overlayfs" error="/var/lib/docker/containerd/daemon/io.containerd.snapshotter.v1.overlayfs does not support d_type. If the backing filesystem is xfs, please reformat with ftype=1 to enable d_type support" module=containerd
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.metadata.v1.bolt"..." module=containerd type=io.containerd.metadata.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=warning msg="could not use snapshotter btrfs in metadata plugin" error="path /var/lib/docker/containerd/daemon/io.containerd.snapshotter.v1.btrfs must be a btrfs filesystem to be used with the btrfs snapshotter" module="containerd/io.containerd.metadata.v1.bolt"
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=warning msg="could not use snapshotter overlayfs in metadata plugin" error="/var/lib/docker/containerd/daemon/io.containerd.snapshotter.v1.overlayfs does not support d_type. If the backing filesystem is xfs, please reformat with ftype=1 to enable d_type support" module="containerd/io.containerd.metadata.v1.bolt"
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.differ.v1.walking"..." module=containerd type=io.containerd.differ.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.gc.v1.scheduler"..." module=containerd type=io.containerd.gc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.containers"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.content"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.diff"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.events"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.healthcheck"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.images"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.leases"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.namespaces"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.snapshots"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.monitor.v1.cgroups"..." module=containerd type=io.containerd.monitor.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.runtime.v1.linux"..." module=containerd type=io.containerd.runtime.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.tasks"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.version"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="loading plugin "io.containerd.grpc.v1.introspection"..." module=containerd type=io.containerd.grpc.v1
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg=serving... address="/var/run/docker/containerd/docker-containerd-debug.sock" module="containerd/debug"
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg=serving... address="/var/run/docker/containerd/docker-containerd.sock" module="containerd/grpc"
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11+08:00" level=info msg="containerd successfully booted in 0.001677s" module=containerd
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11.679808183+08:00" level=warning msg="devmapper: Usage of loopback devices is strongly discouraged for production use. Please use `--storage-opt dm.thinpooldev` or use `man dockerd` to refer to dm.thinpooldev section."
Apr 3 15:31:11 Docker kernel: bio: create slab <bio-2> at 2
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11.835565555+08:00" level=info msg="devmapper: Creating filesystem xfs on device docker-253:1-34265854-base, mkfs args: [-m crc=0,finobt=0 /dev/mapper/docker-253:1-34265854-base]"
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11.836336636+08:00" level=info msg="devmapper: Error while creating filesystem xfs on device docker-253:1-34265854-base: exit status 1"
Apr 3 15:31:11 Docker dockerd: time="2018-04-03T15:31:11.836350296+08:00" level=error msg="[graphdriver] prior storage driver devicemapper failed: exit status 1"
Apr 3 15:31:11 Docker dockerd: Error starting daemon: error initializing graphdriver: exit status 1
Apr 3 15:31:11 Docker systemd: docker.service: main process exited, code=exited, status=1/FAILURE
Apr 3 15:31:11 Docker systemd: Failed to start Docker Application Container Engine.
Apr 3 15:31:11 Docker systemd: Unit docker.service entered failed state.
Apr 3 15:31:11 Docker systemd: docker.service failed.
Apr 3 15:31:12 Docker systemd: docker.service holdoff time over, scheduling restart.

------------------------------------------------

 

重点在红字加粗部分

百度，BING 国内国际都搜过，无有效帮助信息。

再琢磨 异常日志，注意到 mkfs，遂手动执行了下：

mkfs.xfs -m crc=0,finobt=0 /dev/mapper/docker-253:1-34265854-base

报：
=================================

[root@Docker ~]# mkfs.xfs -m crc=0,finobt=0 /dev/mapper/docker-253:1-34265854-base

unknown option -m finobt=0
Usage: mkfs.xfs

--------------------------------

man mkfs.xfs 了下，的确没有 -m 参数

但...查了下网络资料，发现别人的 man mkfs.xfs 资料有-m参数

问题原因：

===========================================
很明显了：mkfs.xfs版本太低，遂更新：
yum update xfsprogs
重启docker服务，正常！

===================================

排查这个问题用了一天...不才不才。做个记录，希望对遇到同样问题的你们有用。