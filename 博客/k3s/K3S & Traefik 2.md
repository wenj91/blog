# [K3S & Traefik 2](https://medium.com/@fache.loic/k3s-traefik-2-9b4646393a1c)

## K3S & Traefik 2

[**K3S](https://k3s.io/) **is a great tool if you want to use Kubernetes in IoT or Edge Computing environments or also in a development environment.

Basically, the service is installed with [**Traefik](https://containo.us/traefik/) **to manage your Ingress routes. However, the installed version is [currently version 1.7.2](https://github.com/helm/charts/tree/master/stable/traefik#configuration) instead of the stable 2.x version.

How to install Traefik in version 2 with this light distribution ? Let’s look at this together 😀
>  **This guide currently works with Traefik 2.2 but you should be able to use it without problems with future versions. **If not, I will update it ! 😉

But before all that, what is K3S and Traefik and why you should use them ?

### What is k3s ?

K3S is a kubernetes certified distribution built for IoT & Edge computing but also for development environments. Why ?

Because **K3S** is much lighter than K8S. Lightweight Kubernetes : Easy to install, half the memory, all in a binary of less than 100 MB.

External dependencies have been minimized (just a modern kernel and cgroup mounts needed) and everything you need is in a single binary !

It is also possible to manage a Kubernetes cluster very easily with K3S.

And to manage the publication of our applications? Traefik is on the network, what K3S is on Kubernetes !

### Makes Networking Boring

Traefik is the leading open source reverse proxy and load balancer for HTTP and TCP-based applications. Why ?

Like K3S, it’s easy to use, dynamic, automatic and has the features you will need ! Like what ?

* Integrates with every major cluster technology,

* Automatic service discovery ,

* Tracing/Metrics,

* HTTPS with Let’s encrypt or custom certificates,

* HTTP/TCP and UDP support,

* Customize your routes with Middlewares,

* Canary deployments,

* Mirroring requests.

Now let’s go to the technique ! 😁

### Install K3S

First, it’s necessary to install K3S in your environment. This is simply done with the following command :

    curl -sfL [https://get.k3s.io](https://get.k3s.io) | sh -s - --disable=traefik

We are not going to deploy Traefik since we want to install our own version 2.2 !

If you don’t have any environment available to perform these tests, you can use multipass , which I recommend, or k3d by example.

Once the installation is over , you can validate it with the following command :

    sudo kubectl get nodes

Now we can integrate Traefik 2.2 into our environment !

### Deploy Traefik 2.2

We will perform this installation without a deployment utility. You can find a helm repo here if you want use an automated install.

First, we need to declare our Ingress resource. This resource has specific elements like Middleware, TCP and UDP routes, TLS options, etc.

We will create the definitions for these resources using ***Custom Resource Definition***( aka CRD ).

All these definitions can be found in the Traefik documentation : [https://docs.traefik.io/user-guides/crd-acme/](https://docs.traefik.io/user-guides/crd-acme/)

I will only take them back, in a file CustomResourceDefinition.yaml, to apply them :

```yaml
apiVersion: apiextensions.k8s.io/v1beta1
kind: CustomResourceDefinition
metadata:
  name: ingressroutes.traefik.containo.us

spec:
  group: traefik.containo.us
  version: v1alpha1
  names:
    kind: IngressRoute
    plural: ingressroutes
    singular: ingressroute
  scope: Namespaced

---
apiVersion: apiextensions.k8s.io/v1beta1
kind: CustomResourceDefinition
metadata:
  name: middlewares.traefik.containo.us

spec:
  group: traefik.containo.us
  version: v1alpha1
  names:
    kind: Middleware
    plural: middlewares
    singular: middleware
  scope: Namespaced

---
apiVersion: apiextensions.k8s.io/v1beta1
kind: CustomResourceDefinition
metadata:
  name: ingressroutetcps.traefik.containo.us

spec:
  group: traefik.containo.us
  version: v1alpha1
  names:
    kind: IngressRouteTCP
    plural: ingressroutetcps
    singular: ingressroutetcp
  scope: Namespaced

---
apiVersion: apiextensions.k8s.io/v1beta1
kind: CustomResourceDefinition
metadata:
  name: ingressrouteudps.traefik.containo.us

spec:
  group: traefik.containo.us
  version: v1alpha1
  names:
    kind: IngressRouteUDP
    plural: ingressrouteudps
    singular: ingressrouteudp
  scope: Namespaced

---
apiVersion: apiextensions.k8s.io/v1beta1
kind: CustomResourceDefinition
metadata:
  name: tlsoptions.traefik.containo.us

spec:
  group: traefik.containo.us
  version: v1alpha1
  names:
    kind: TLSOption
    plural: tlsoptions
    singular: tlsoption
  scope: Namespaced

---
apiVersion: apiextensions.k8s.io/v1beta1
kind: CustomResourceDefinition
metadata:
  name: tlsstores.traefik.containo.us

spec:
  group: traefik.containo.us
  version: v1alpha1
  names:
    kind: TLSStore
    plural: tlsstores
    singular: tlsstore
  scope: Namespaced

---
apiVersion: apiextensions.k8s.io/v1beta1
kind: CustomResourceDefinition
metadata:
  name: traefikservices.traefik.containo.us

spec:
  group: traefik.containo.us
  version: v1alpha1
  names:
    kind: TraefikService
    plural: traefikservices
    singular: traefikservice
  scope: Namespaced

---
kind: ClusterRole
apiVersion: rbac.authorization.k8s.io/v1beta1
metadata:
  name: traefik-ingress-controller

rules:
  - apiGroups:
      - ""
    resources:
      - services
      - endpoints
      - secrets
    verbs:
      - get
      - list
      - watch
  - apiGroups:
      - extensions
    resources:
      - ingresses
    verbs:
      - get
      - list
      - watch
  - apiGroups:
      - extensions
    resources:
      - ingresses/status
    verbs:
      - update
  - apiGroups:
      - traefik.containo.us
    resources:
      - middlewares
      - ingressroutes
      - traefikservices
      - ingressroutetcps
      - ingressrouteudps
      - tlsoptions
      - tlsstores
    verbs:
      - get
      - list
      - watch

---
kind: ClusterRoleBinding
apiVersion: rbac.authorization.k8s.io/v1beta1
metadata:
  name: traefik-ingress-controller

roleRef:
  apiGroup: rbac.authorization.k8s.io
  kind: ClusterRole
  name: traefik-ingress-controller
subjects:
  - kind: ServiceAccount
    name: traefik-ingress-controller
    namespace: default
```

Finally I can apply my file :

sudo kubectl apply -f ./CustomeResourceDefinition.yaml

Output :

    customresourcedefinition.apiextensions.k8s.io/ingressroutes.traefik.containo.us created
    customresourcedefinition.apiextensions.k8s.io/middlewares.traefik.containo.us created
    customresourcedefinition.apiextensions.k8s.io/ingressroutetcps.traefik.containo.us created
    customresourcedefinition.apiextensions.k8s.io/ingressrouteudps.traefik.containo.us created
    customresourcedefinition.apiextensions.k8s.io/tlsoptions.traefik.containo.us created
    customresourcedefinition.apiextensions.k8s.io/tlsstores.traefik.containo.us created
    customresourcedefinition.apiextensions.k8s.io/traefikservices.traefik.containo.us created
    clusterrole.rbac.authorization.k8s.io/traefik-ingress-controller created
    clusterrolebinding.rbac.authorization.k8s.io/traefik-ingress-controller created

We will be able to proceed to the declaration of our deployment. Here is my file deployment.yaml :

 <iframe src="https://medium.com/media/d02c7f4cd508baa1815da7b9754ef708" frameborder=0></iframe>

You can use this file “out of the box” but remember to change your email address on :

--certificateresolvers.myresolver.acme.email=

Again, we can apply this configuration with :

sudo kubectl apply -f ./deployment.yaml

Output :

    service/traefik created
    serviceaccount/traefik-ingress-controller created
    deployment.apps/traefik created

It’s possible to check your installation with a web browser by going to the following address : http://<Traefik_IP>:8080/dashboard/

![Traefik 2.2 Dashboard](https://cdn-images-1.medium.com/max/2884/1*mXKSH_LGyoYitDaYEzmDdQ.png)

Now deploy an application to validate the proper functioning of our Ingress route !

### Deploy whoami example

I’m just going to use a whoami image from Containous. First let’s create our whoami.yaml file :

```yaml
apiVersion: v1
kind: Service
metadata:
 name: traefik
spec:
 ports:
 - protocol: TCP
   name: web
   port: 80
 - protocol: TCP
   name: admin
   port: 8080
 - protocol: TCP
   name: websecure
   port: 443
 type: LoadBalancer
 selector:
  app: traefik
---
apiVersion: v1
kind: ServiceAccount
metadata:
 namespace: default
 name: traefik-ingress-controller

---
apiVersion: apps/v1
kind: Deployment
metadata:
  namespace: default
  name: traefik
  labels:
    app: traefik

spec:
  replicas: 1
  selector:
    matchLabels:
      app: traefik
  template:
    metadata:
      labels:
        app: traefik
    spec:
      serviceAccountName: traefik-ingress-controller
      containers:
       - name: traefik
         image: traefik:v2.2
         args:
            - --api.insecure
            - --accesslog
            - --entrypoints.web.Address=:80
            - --entrypoints.websecure.Address=:443
            - --providers.kubernetescrd
            - --certificatesresolvers.myresolver.acme.tlschallenge
            - --certificatesresolvers.myresolver.acme.email=foo@you.com
            - --certificatesresolvers.myresolver.acme.storage=acme.json
         ports:
            - name: web
              containerPort: 80
            - name: websecure
              containerPort: 443
            - name: admin
              containerPort: 8080
```

For this example, I will use the domain mydomain.com. You must modify the following element in order to match it to your domain :

- match: Host(`mydomain.com`)

Again :

sudo kubectl apply -f ./whoami.yaml

You can validate this route in your dashboard or directly via the command :

    $ curl -I [http://mydomain.com](http://mydomain.com)
    Hostname: whoami-app-84d8fbcf48-l87fj
    IP: 127.0.0.1
    IP: ::1
    IP: 10.42.0.10
    IP: fe80::2881:b7ff:fe6b:318c
    RemoteAddr: 10.42.0.9:44436
    GET / HTTP/1.1
    Host: mydomain.com
    User-Agent: curl/7.64.0
    Accept: */*
    Accept-Encoding: gzip
    X-Forwarded-For: 10.42.0.1
    X-Forwarded-Host: mydomain.com
    X-Forwarded-Port: 80
    X-Forwarded-Proto: http
    X-Forwarded-Server: traefik-7df7bc4665-cqbbs
    X-Real-Ip: 10.42.0.1

And now ? We can add https to our app !

### HTTPS Everywhere

So let’s modify our whoami.yaml file to add our entrypoint for HTTPS :

 ```yaml
 apiVersion: traefik.containo.us/v1alpha1
kind: IngressRoute
metadata:
  name: ingressroutetls
spec:
  entryPoints:
    - websecure
  routes:
  - match: Host(`mydomain.com`)
    kind: Rule
    services:
    - name: whoami-app
      port: 80
  tls: # This route uses TLS
      certResolver: myresolver
 ```

After apply, you can check if all works fine with that command :

    $ curl [https://m](https://whoami.dubarbu.fr)ydomain.com
    Hostname: whoami-app-84d8fbcf48-l87fj
    IP: 127.0.0.1
    IP: ::1
    IP: 10.42.0.10
    IP: fe80::2881:b7ff:fe6b:318c
    RemoteAddr: 10.42.0.9:44436
    GET / HTTP/1.1
    Host: mydomain.com
    User-Agent: curl/7.64.0
    Accept: */*
    Accept-Encoding: gzip
    X-Forwarded-For: 10.42.0.1
    X-Forwarded-Host: mydomain.com
    X-Forwarded-Port: 443
    X-Forwarded-Proto: https
    X-Forwarded-Server: traefik-7df7bc4665-cqbbs
    X-Real-Ip: 10.42.0.1

In addition to adding HTTPS to our application, we can redirect the HTTP flow to HTTPS automatically.

To do this, I will use a [new feature in **Traefik 2.2](https://containo.us/blog/traefik-2-2-ingress/)**. The possibility of performing a redirect directly on HTTP entrypoint !

Let’s modify -a bit- the configuration of our Traefik instance :

```yaml
containers:
   - name: traefik
     image: traefik:v2.2
     args:
        - --api.insecure
        - --accesslog
        - --entrypoints.web.Address=:80
        - --entrypoints.websecure.Address=:443
        - --providers.kubernetescrd
        - --entrypoints.web.http.redirections.entryPoint.to=:443
        - --entrypoints.web.http.redirections.entryPoint.scheme=https
        - --certificatesresolvers.myresolver.acme.tlschallenge
        - --certificatesresolvers.myresolver.acme.email=foo@you.com
        - --certificatesresolvers.myresolver.acme.storage=acme.json
```
We only added two lines !

* --entrypoints.web.http.redirections.entryPoint.to=:443

* --entrypoints.web.http.redirections.entryPoint.scheme=https

With those lines, you say, all incoming from entrypoints.web redirect to entryPoint.to=:443 .

And this requests are now on https with : entryPoint.scheme=https

Last check today :

    $ curl -I http://mydomain.com
    HTTP/1.1 308 Permanent Redirect
    Location: [https://mydomain.com/](https://whoami.dubarbu.fr/)
    Date: Tue, 26 May 2020 21:05:20 GMT
    Content-Length: 18
    Content-Type: text/plain; charset=utf-8

As you can see, it’s very easy to integrate Traefik 2.2 on K3S !

You have several solutions to achieve this, such as using [Helm](https://github.com/containous/traefik-helm-chart) or may be great tool from [Alex Ellis](undefined) : [Arkade](https://github.com/alexellis/arkade).

Today, we were able to see how to integrate all of this manually to better understand how it works.

Now, you just have to choose the best solution for your needs !

In any case, using **K3S** and **Traefik** will allow you to easily deploy your applications wherever you want ! 😍

In a future article, we will see how to easily deploy a Nexctloud instance with Docker and Traefik !
