# [k8s service](https://stackoverflow.com/questions/47027194/how-to-access-a-service-in-a-kubernetes-cluster-using-the-service-name)

As long as `kube-dns` is running (which I believe is "always unless you disable it"), all Service objects have an _in cluster_ DNS name of `service_name +"."+ service_namespace + ".svc.cluster.local"` so all other things would address your `backendapi` in the `default` namespace as (to use your port numbered example) `http://backendapi.default.svc.cluster.local:8080`. That fact is the very reason Kubernetes forces all identifiers to be a "dns compatible" name (no underscores or other goofy characters).

Even if you are _not_ running kube-dns, all Service names and ports are also injected into the environment of Pods just like docker would do, so the environment variables `${BACKENDAPI_SERVICE_HOST}:${BACKENDAPI_SERVICE_PORT}` would contain the Service's in-cluster IP (even though the env-var is named "host") and the "default" Service port (8080 in your example) if there is only one.

Whether you choose to use the DNS name or the environment-variable-ip is a matter of whether you like having the "readable" names for things in log output or error messages, versus whether you prefer to skip the DNS lookup and use the Service IP address for speed but less legibility. They _behave_ the same.

The whole story lives [in the services-networking concept documentation](https://kubernetes.io/docs/concepts/services-networking/service/)