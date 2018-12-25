# [Kubernetes Pods](https://kubernetes.io/docs/tutorials/kubernetes-basics/explore/explore-intro/)
When you created a Deployment in Module 2, Kubernetes created a Pod to host your application instance. A Pod is a Kubernetes abstraction that represents a group of one or more application containers (such as Docker or rkt), and some shared resources for those containers. Those resources include:

## Shared storage, as Volumes
## Networking, as a unique cluster IP address
## Information about how to run each container, such as the container image version or specific ports to use