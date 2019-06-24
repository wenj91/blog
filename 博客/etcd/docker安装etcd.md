# Docker
etcd uses gcr.io/etcd-development/etcd as a primary container registry, and quay.io/coreos/etcd as secondary.

```bash
rm -rf /data/etcd/etcd-data.tmp && mkdir -p /data/etcd/etcd-data.tmp && \
  docker rmi quay.io/coreos/etcd:v3.3.13 || true && \
  docker run \
  -p 2379:2379 \
  -p 2380:2380 \
  --mount type=bind,source=/data/etcd/etcd-data.tmp,destination=/etcd-data \
  --name etcd-gcr-v3.3.13 \
  quay.io/coreos/etcd:v3.3.13 \
  /usr/local/bin/etcd \
  --name s1 \
  --data-dir /etcd-data \
  --listen-client-urls http://0.0.0.0:2379 \
  --advertise-client-urls http://0.0.0.0:2379 \
  --listen-peer-urls http://0.0.0.0:2380 \
  --initial-advertise-peer-urls http://0.0.0.0:2380 \
  --initial-cluster s1=http://0.0.0.0:2380 \
  --initial-cluster-token tkn \
  --initial-cluster-state new

docker exec etcd-gcr-v3.3.13 /bin/sh -c "/usr/local/bin/etcd --version"
docker exec etcd-gcr-v3.3.13 /bin/sh -c "ETCDCTL_API=3 /usr/local/bin/etcdctl version"
docker exec etcd-gcr-v3.3.13 /bin/sh -c "ETCDCTL_API=3 /usr/local/bin/etcdctl endpoint health"
docker exec etcd-gcr-v3.3.13 /bin/sh -c "ETCDCTL_API=3 /usr/local/bin/etcdctl put foo bar"
docker exec etcd-gcr-v3.3.13 /bin/sh -c "ETCDCTL_API=3 /usr/local/bin/etcdctl get foo"
```