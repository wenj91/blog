# [k3s部署cert-manager v1.4.0](https://cert-manager.io/docs/installation/kubernetes/)

Installing with regular manifests[](#)
--------------------------------------

All resources (the `CustomResourceDefinitions`, cert-manager, namespace, and the webhook component) are included in a single YAML manifest file:

> **Note**: If you’re using a `kubectl` version below `v1.19.0-rc.1` you will have issues updating the CRDs. For more info see the [v0.16 upgrade notes](https://cert-manager.ioinstallation/upgrading/upgrading-0.15-0.16/#issue-with-older-versions-of-kubectl)

Install the `CustomResourceDefinitions` and cert-manager itself:

    $ kubectl apply -f https://github.com/jetstack/cert-manager/releases/download/v1.4.0/cert-manager.yaml
    

> **Note**: When running on GKE (Google Kubernetes Engine), you may encounter a ‘permission denied’ error when creating some of these resources. This is a nuance of the way GKE handles RBAC and IAM permissions, and as such you should ‘elevate’ your own privileges to that of a ‘cluster-admin’ **before** running the above command. If you have already run the above command, you should run them again after elevating your permissions:

      kubectl create clusterrolebinding cluster-admin-binding \
        --clusterrole=cluster-admin \
        --user=$(gcloud config get-value core/account)
    

> **Note**: By default, cert-manager will be installed into the `cert-manager` namespace. It is possible to run cert-manager in a different namespace, although you will need to make modifications to the deployment manifests.

Once you have deployed cert-manager, you can verify the installation [here](https://cert-manager.io/docs/installation/kubernetes/#verifying-the-installation).

Installing with Helm[](#)
-------------------------

As an alternative to the YAML manifests referenced above, we also provide an official Helm chart for installing cert-manager.

> **Note**: cert-manager should never be embedded as a sub-chart into other Helm charts. cert-manager manages non-namespaced resources in your cluster and should only be installed once.

### Prerequisites[](#)

*   Helm v3 installed

### Steps[](#)

In order to install the Helm chart, you must follow these steps:

Create the namespace for cert-manager:

    $ kubectl create namespace cert-manager
    

Add the Jetstack Helm repository:

> **Warning**: It is important that this repository is used to install cert-manager. The version residing in the helm stable repository is _deprecated_ and should _not_ be used.

    $ helm repo add jetstack https://charts.jetstack.io
    

Update your local Helm chart repository cache:

    $ helm repo update
    

cert-manager requires a number of CRD resources to be installed into your cluster as part of installation.

This can either be done manually, using `kubectl`, or using the `installCRDs` option when installing the Helm chart.

> **Note**: If you’re using a `helm` version based on Kubernetes `v1.18` or below (Helm `v3.2`) `installCRDs` will not work with cert-manager `v0.16`. For more info see the [v0.16 upgrade notes](https://cert-manager.ioinstallation/upgrading/upgrading-0.15-0.16/#helm)

#### Option 1: installing CRDs with `kubectl`[](#)

Install the `CustomResourceDefinition` resources using `kubectl`:

    $ kubectl apply -f https://github.com/jetstack/cert-manager/releases/download/v1.4.0/cert-manager.crds.yaml
    

#### Option 2: install CRDs as part of the Helm release[](#)

To automatically install and manage the CRDs as part of your Helm release, you must add the `--set installCRDs=true` flag to your Helm installation command.

Uncomment the relevant line in the next steps to enable this.

* * *

To install the cert-manager Helm chart:

    $ helm install \
      cert-manager jetstack/cert-manager \
      --namespace cert-manager \
      --create-namespace \
      --version v1.4.0 \
      # --set installCRDs=true

    or

    $ helm install \
        cert-manager cert-manager-v1.4.0.tgz \
        --namespace cert-manager \
        --version v1.4.0 
        # --set installCRDs=true

注意：这里如果发生错误

    Error: rendered manifests contain a resource that already exists. Unable to continue with install: ServiceAccount "cert-manager-cainjector" in namespace "cert-manager" exists and cannot be imported into the current release: invalid ownership metadata; annotation validation error: missing key "meta.helm.sh/release-name": must be set to "cert-manager"; annotation validation error: missing key "meta.helm.sh/release-namespace": must be set to "cert-manager"

不用惊慌，手动执行就可以成功：

    $ helm template cert-manager jetstack/cert-manager --namespace cert-manager | kubectl apply -f -

    serviceaccount/cert-manager-cainjector created
    serviceaccount/cert-manager created
    serviceaccount/cert-manager-webhook created
    clusterrole.rbac.authorization.k8s.io/cert-manager-cainjector unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-controller-ingress-shim unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-controller-clusterissuers unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-controller-issuers unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-controller-certificates unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-edit unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-controller-challenges unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-controller-orders unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-view unchanged
    clusterrole.rbac.authorization.k8s.io/cert-manager-webhook:webhook-requester unchanged
    clusterrolebinding.rbac.authorization.k8s.io/cert-manager-cainjector unchanged
    clusterrolebinding.rbac.authorization.k8s.io/cert-manager-controller-orders unchanged
    clusterrolebinding.rbac.authorization.k8s.io/cert-manager-controller-issuers unchanged
    clusterrolebinding.rbac.authorization.k8s.io/cert-manager-controller-clusterissuers unchanged
    clusterrolebinding.rbac.authorization.k8s.io/cert-manager-controller-certificates unchanged
    clusterrolebinding.rbac.authorization.k8s.io/cert-manager-controller-challenges unchanged
    clusterrolebinding.rbac.authorization.k8s.io/cert-manager-controller-ingress-shim unchanged
    clusterrolebinding.rbac.authorization.k8s.io/cert-manager-webhook:auth-delegator configured
    role.rbac.authorization.k8s.io/cert-manager-cainjector:leaderelection unchanged
    role.rbac.authorization.k8s.io/cert-manager:leaderelection unchanged
    rolebinding.rbac.authorization.k8s.io/cert-manager-cainjector:leaderelection unchanged
    rolebinding.rbac.authorization.k8s.io/cert-manager:leaderelection configured
    rolebinding.rbac.authorization.k8s.io/cert-manager-webhook:webhook-authentication-reader configured
    service/cert-manager created
    service/cert-manager-webhook created
    deployment.apps/cert-manager-cainjector created
    deployment.apps/cert-manager created
    deployment.apps/cert-manager-webhook created
    mutatingwebhookconfiguration.admissionregistration.k8s.io/cert-manager-webhook configured
    validatingwebhookconfiguration.admissionregistration.k8s.io/cert-manager-webhook configured


到这里准备工作已经好了，然后创建ClusterIssuer

    $ cat <<EOF > letsencrypt-prod-issuer.yaml
    apiVersion: cert-manager.io/v1alpha2
    kind: ClusterIssuer
    metadata:
    name: letsencrypt-prod
    spec:
    acme:
        # You must replace this email address with your own.
        # Let's Encrypt will use this to contact you about expiring
        # certificates, and issues related to your account.
        email: xx@gmail.com
        server: https://acme-v02.api.letsencrypt.org/directory
        privateKeySecretRef:
        # Secret resource used to store the account's private key.
        name: letsencrypt-prod
        # Add a single challenge solver, HTTP01 using nginx
        solvers:
        - http01:
            ingress:
                class: traefik
    EOF

然后创建证书Certificate

    $ cat <<EOF > le-test-certificate.yaml
    apiVersion: cert-manager.io/v1alpha2
    kind: Certificate
    metadata:
    name: nginx-wenj91
    namespace: default
    spec:
    secretName: nginx-wenj91-tls # 证书保存的 secret 名
    duration: 2160h # 90d
    renewBefore: 360h # 15d
    organization:
        - jetstack
    isCA: false
    keySize: 2048
    keyAlgorithm: rsa
    keyEncoding: pkcs1
    dnsNames:
        - nginx.xx.com
    issuerRef: # 指定ClusterIssuer
        name: letsencrypt-prod
        kind: ClusterIssuer 
        group: cert-manager.io
    EOF

创建test-nginx

    $ cat <<EOF > test-nginx.yaml
    apiVersion: apps/v1
    kind: Deployment
    metadata:
    name: my-nginx
    namespace: default
    labels:
        app: my-nginx
    spec:
    selector:
        matchLabels:
        app: my-nginx
    replicas: 1
    template:
        metadata:
        labels:
            app: my-nginx
        spec:
        containers:
            - image: nginx # 使用 nginx 的 docker image
            name: my-nginx
            ports:
                - containerPort: 80 # 暴露 nginx 的监听端口
                name: "my-nginx"
                protocol: "TCP"
    ---
    apiVersion: v1
    kind: Service
    metadata:
    name: my-nginx
    namespace: default
    labels:
        app: my-nginx
    spec:
    ports:
        - name: http
        port: 80 # service 的监听端口
        protocol: TCP
        targetPort: 80 # Pod 的监听端口
    selector:
        app: my-nginx
    ---
    apiVersion: extensions/v1beta1
    kind: Ingress
    metadata:
    name: my-nginx
    namespace: default
    annotations:
        kubernetes.io/ingress.class: "traefik" # 注意：在创建 Issuer 资源的时候有配置 ingress.class，需要保持一直
        cert-manager.io/cluster-issuer: letsencrypt-prod # 指定 cert-manager 的 Issuer 的名字
        kubernetes.io/tls-acme: "true" # 可选
        ingress.kubernetes.io/ssl-redirect: "true" # 强制的从 HTTP 重定向刀 HTTPS
    spec:
    rules:
        - host: nginx.xx.com
        http:
            paths:
            - backend:
                serviceName: my-nginx
                servicePort: 80
                path: /
    tls:
        - secretName: nginx-wenj91-tls
        hosts:
            - nginx.xx.com
    EOF

至此，自带https的nginx.xx.com已经成功创建，打开浏览器输入`https://nginx.xx.com`会出现如下：

    Welcome to nginx!

    If you see this page, the nginx web server is successfully installed and working. Further configuration is required.

    For online documentation and support please refer to nginx.org.
    Commercial support is available at nginx.com.

    Thank you for using nginx.




The default cert-manager configuration is good for the majority of users, but a full list of the available options can be found in the [Helm chart README](https://artifacthub.io/packages/helm/cert-manager/cert-manager).

Installing with Operator Lifecycle Manager and OperatorHub.io[](#)
------------------------------------------------------------------

Browse to the [cert-manager page on OperatorHub.io](https://operatorhub.io/operator/cert-manager), click the “Install” button and follow the installation instructions.

Alternatively, [install OLM](https://olm.operatorframework.io/docs/getting-started/) and [Krew Kubectl plugins index](https://github.com/operator-framework/kubectl-operator>install the <code>kubectl operator</code> plugin</a>
from the <a href=) and then use that to install the cert-manager as follows:

    operator-sdk olm install
    kubectl krew install operator
    kubectl operator install cert-manager -n operators --channel stable --approval Automatic
    

You can monitor the progress of the installation as follows:

    kubectl get events -w -n operators
    

And you can see the status of the installation with:

    kubectl operator list
    

### Release Channels[](#)

Whichever installation method you chose, there will now be an [OLM Subscription resource](https://olm.operatorframework.io/docs/concepts/crds/subscription/) for cert-manager, tracking the “stable” release channel. E.g.

    $ kubectl get subscription cert-manager -n operators -o yaml
    ...
    spec:
      channel: stable
      installPlanApproval: Automatic
      name: cert-manager
    ...
    status:
      currentCSV: cert-manager.v1.4.0
      state: AtLatestKnown
    ...
    

This means that OLM will discover new cert-manager releases in the stable channel, and, depending on the Subscription settings it will upgrade cert-manager automatically, when new releases become available. Read [OLM’s Recommended Channel Naming](https://olm.operatorframework.io/docs/concepts/crds/subscription/>Manually Approving Upgrades via Subscriptions</a> for information about automatic and manual upgrades.</p>
<p>NOTE: There is a single release channel called “stable” which will contain all cert-manager releases, shortly after they are released.
In future we may introduce other release channels with alternative release schedules,
in accordance with <a href=).

Verifying the installation[](#)
-------------------------------

Once you’ve installed cert-manager, you can verify it is deployed correctly by checking the `cert-manager` namespace for running pods:

    $ kubectl get pods --namespace cert-manager
    
    NAME                                       READY   STATUS    RESTARTS   AGE
    cert-manager-5c6866597-zw7kh               1/1     Running   0          2m
    cert-manager-cainjector-577f6d9fd7-tr77l   1/1     Running   0          2m
    cert-manager-webhook-787858fcdb-nlzsq      1/1     Running   0          2m
    

You should see the `cert-manager`, `cert-manager-cainjector`, and `cert-manager-webhook` pod in a `Running` state. It may take a minute or so for the TLS assets required for the webhook to function to be provisioned. This may cause the webhook to take a while longer to start for the first time than other pods. If you experience problems, please check the [FAQ guide](https://cert-manager.iodocs/installation/faq/).

The following steps will confirm that cert-manager is set up correctly and able to issue basic certificate types.

Create an `Issuer` to test the webhook works okay.

    $ cat <<EOF > test-resources.yaml
    apiVersion: v1
    kind: Namespace
    metadata:
      name: cert-manager-test
    ---
    apiVersion: cert-manager.io/v1
    kind: Issuer
    metadata:
      name: test-selfsigned
      namespace: cert-manager-test
    spec:
      selfSigned: {}
    ---
    apiVersion: cert-manager.io/v1
    kind: Certificate
    metadata:
      name: selfsigned-cert
      namespace: cert-manager-test
    spec:
      dnsNames:
        - example.com
      secretName: selfsigned-cert-tls
      issuerRef:
        name: test-selfsigned
    EOF
    

Create the test resources.

    $ kubectl apply -f test-resources.yaml
    

Check the status of the newly created certificate. You may need to wait a few seconds before cert-manager processes the certificate request.

    $ kubectl describe certificate -n cert-manager-test
    
    ...
    Spec:
      Common Name:  example.com
      Issuer Ref:
        Name:       test-selfsigned
      Secret Name:  selfsigned-cert-tls
    Status:
      Conditions:
        Last Transition Time:  2019-01-29T17:34:30Z
        Message:               Certificate is up to date and has not expired
        Reason:                Ready
        Status:                True
        Type:                  Ready
      Not After:               2019-04-29T17:34:29Z
    Events:
      Type    Reason      Age   From          Message
      ----    ------      ----  ----          -------
      Normal  CertIssued  4s    cert-manager  Certificate issued successfully
    

Clean up the test resources.

    $ kubectl delete -f test-resources.yaml
    

If all the above steps have completed without error, you are good to go!

Optionally the whole verification flow is automated by running tool maintained by the community [cert-manager-verifier](https://github.com/alenkacz/cert-manager-verifier).

If you experience problems, please check the [FAQ](https://cert-manager.iodocs/installation/faq/).

Configuring your first Issuer[](#)
----------------------------------

Before you can begin issuing certificates, you must configure at least one `Issuer` or `ClusterIssuer` resource in your cluster.

You should read the [configuration](https://cert-manager.iodocs/installation/configuration/) guide to learn how to configure cert-manager to issue certificates from one of the supported backends.

Installing the kubectl plugin[](#)
----------------------------------

cert-manager also has a kubectl plugin which can be used to help you to manage cert-manager resources in the cluster. Installation instructions for this can be found in the [kubectl plugin](https://cert-manager.iodocs/installation/usage/kubectl-plugin/) documentation.

Alternative installation methods[](#)
-------------------------------------