# [k3s Traefik Dashboard](https://randy-stad.gitlab.io/posts/2020-01-29-k3s-traefik-dashboard/)

[Traefik](https://containo.us/traefik/) is automatically deployed as part of the [k3s](https://k3s.io) Kubernetes cluster. To enable the dashboard for Traefik follow these instructions.

### Enable the Dashboard

The dashboard is not enabled in the base k3s distribution. Enable the dashboard by editing the traefik.yaml manifest at `/var/lib/rancher/k3s/server/manifests`:

    sudo vi /var/lib/rancher/k3s/server/manifests/traefik.yaml

Add the line `dashboard.enabled: "true"` in the spec: set: section. Remember this is YAML so match the indent of the previous line.

Save the file and k3s will deploy the dashboard service, you can see the service with the kubectl get service command:

    kubectl get service --all-namespaces
    NAMESPACE     NAME                 TYPE           CLUSTER-IP      EXTERNAL-IP     PORT(S)                      AGE
    default       kubernetes           ClusterIP      10.43.0.1       <none>          443/TCP                      3d5h
    kube-system   kube-dns             ClusterIP      10.43.0.10      <none>          53/UDP,53/TCP,9153/TCP       3d5h
    kube-system   metrics-server       ClusterIP      10.43.60.8      <none>          443/TCP                      3d5h
    kube-system   traefik-prometheus   ClusterIP      10.43.215.249   <none>          9100/TCP                     3d5h
    kube-system   traefik              LoadBalancer   10.43.204.45    192.168.1.109   80:31930/TCP,443:32219/TCP   3d5h
    kube-system   traefik-dashboard    ClusterIP      10.43.72.66     <none>          80/TCP                       103m

Note the CLUSTER-IP address, if you can open a web browser on your master node, just navigate to that address. I have a headless master node so I do a reverse ssh proxy to my mac so I can access the dashboard there. To set up the reverse proxy, note the CLUSTER-IP address of the traefik-dashboard service and use it in this command line (it will different if you use a different cluster CIDR for your network). You need to set up remote login on your mac or equivalent machine to make this work.

    sudo ssh -R 8080:<CLUSTER-IP>:80 user@mac

On your external box, navigate to `http:\\localhost:8080\dashboard\` and you will see the Traefik dashboard.

[kubernetes](https://randy-stad.gitlab.io//tags/kubernetes/)  [k3s](https://randy-stad.gitlab.io//tags/k3s/)  [traefik](https://randy-stad.gitlab.io//tags/traefik/)