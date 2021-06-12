# [K8S基于ingress-nginx实现灰度发布](https://www.cnblogs.com/xiaoqi/p/ingress-nginx-canary.html)

[K8S基于ingress-nginx实现灰度发布](https://www.cnblogs.com/xiaoqi/p/ingress-nginx-canary.html)
======================================================================================

Ingress-Nginx 是一个K8S ingress工具，支持配置 Ingress Annotations 来实现不同场景下的灰度发布和测试。本文介绍如何使用 Ingress-Nginx实现灰度发布。

之前介绍过[使用ambassador实现灰度发布](https://www.cnblogs.com/xiaoqi/p/ambassador.html)，今天介绍如何使用ingre-nginx实现。

介绍[#](#)
--------

Ingress-Nginx 是一个K8S ingress工具，支持配置 Ingress Annotations 来实现不同场景下的灰度发布和测试。 [Nginx Annotations](https://kubernetes.github.io/ingress-nginx/user-guide/nginx-configuration/annotations/ target=) 支持以下 4 种 Canary 规则：

*   `nginx.ingress.kubernetes.io/canary-by-header`：基于 Request Header 的流量切分，适用于灰度发布以及 A/B 测试。当 Request Header 设置为 `always`时，请求将会被一直发送到 Canary 版本；当 Request Header 设置为 `never`时，请求不会被发送到 Canary 入口；对于任何其他 Header 值，将忽略 Header，并通过优先级将请求与其他金丝雀规则进行优先级的比较。
*   `nginx.ingress.kubernetes.io/canary-by-header-value`：要匹配的 Request Header 的值，用于通知 Ingress 将请求路由到 Canary Ingress 中指定的服务。当 Request Header 设置为此值时，它将被路由到 Canary 入口。该规则允许用户自定义 Request Header 的值，必须与上一个 annotation (即：canary-by-header）一起使用。
*   `nginx.ingress.kubernetes.io/canary-weight`：基于服务权重的流量切分，适用于蓝绿部署，权重范围 0 - 100 按百分比将请求路由到 Canary Ingress 中指定的服务。权重为 0 意味着该金丝雀规则不会向 Canary 入口的服务发送任何请求。权重为 100 意味着所有请求都将被发送到 Canary 入口。
*   `nginx.ingress.kubernetes.io/canary-by-cookie`：基于 Cookie 的流量切分，适用于灰度发布与 A/B 测试。用于通知 Ingress 将请求路由到 Canary Ingress 中指定的服务的cookie。当 cookie 值设置为 `always`时，它将被路由到 Canary 入口；当 cookie 值设置为 `never`时，请求不会被发送到 Canary 入口；对于任何其他值，将忽略 cookie 并将请求与其他金丝雀规则进行优先级的比较。

注意：金丝雀规则按优先顺序进行如下排序：

> `canary-by-header - > canary-by-cookie - > canary-weight`

我们可以把以上的四个 annotation 规则可以总体划分为以下两类：

*   基于权重的 Canary 规则

[![](https://gitee.com/jadepeng/pic/raw/master/pic/2020/1/17/1579246552422.png)](https://gitee.com/jadepeng/pic/raw/master/pic/2020/1/17/1579246552422.png)

*   基于用户请求的 Canary 规则

[![](https://gitee.com/jadepeng/pic/raw/master/pic/2020/1/17/1579246569310.png)](https://gitee.com/jadepeng/pic/raw/master/pic/2020/1/17/1579246569310.png)

注意： Ingress-Nginx 实在0.21.0 版本 中，引入的Canary 功能，因此要确保ingress版本OK

测试[#](#)
--------

### 应用准备[#](#)

两个版本的服务，正常版本：

Copy

`import static java.util.Collections.singletonMap;

@SpringBootApplication
@Controller
public class RestPrometheusApplication {

	@Autowired
	private MeterRegistry registry;

	@GetMapping(path = "/", produces = "application/json")
	@ResponseBody
	public Map<String, Object> landingPage() {
		Counter.builder("mymetric").tag("foo", "bar").register(registry).increment();
		return singletonMap("hello", "ambassador");
	}

	public static void main(String[] args) {
		SpringApplication.run(RestPrometheusApplication.class, args);
	}

}` 

访问会输出：

Copy

`{"hello":"ambassador"}` 

灰度版本：

Copy

`import static java.util.Collections.singletonMap;

@SpringBootApplication
@Controller
public class RestPrometheusApplication {

	@Autowired
	private MeterRegistry registry;

	@GetMapping(path = "/", produces = "application/json")
	@ResponseBody
	public Map<String, Object> landingPage() {
		Counter.builder("mymetric").tag("foo", "bar").register(registry).increment();
		return singletonMap("hello", "ambassador, this is a gray version");
	}

	public static void main(String[] args) {
		SpringApplication.run(RestPrometheusApplication.class, args);
	}

}` 

访问会输出：

Copy

`{"hello":"ambassador, this is a gray version"}` 

### ingress 配置[#](#)

我们部署好两个服务，springboot-rest-demo是正常的服务，springboot-rest-demo-gray是灰度服务，我们来配置ingress，通过canary-by-header来实现：

正常服务的：

Copy

`apiVersion: extensions/v1beta1
kind: Ingress
metadata:
  name: springboot-rest-demo
  annotations:
    kubernetes.io/ingress.class: nginx
spec:
  rules:
  - host: springboot-rest.jadepeng.com
    http:
      paths:
      - backend:
          serviceName: springboot-rest-demo
          servicePort: 80` 

canary 的：

Copy

`apiVersion: extensions/v1beta1
kind: Ingress
metadata:
  name: springboot-rest-demo-gray
  annotations:
    kubernetes.io/ingress.class: nginx
    nginx.ingress.kubernetes.io/canary: "true"
    nginx.ingress.kubernetes.io/canary-by-header: "canary"
	nginx.ingress.kubernetes.io/canary-by-header-value: "true"
spec:
  rules:
  - host: springboot-rest.jadepeng.com
    http:
      paths:
      - backend:
          serviceName: springboot-rest-demo-gray
          servicePort: 80` 

将上面的文件执行：

Copy

`kubectl -n=default apply -f ingress-test.yml 
ingress.extensions/springboot-rest-demo created
ingress.extensions/springboot-rest-demo-gray created` 

执行测试，不添加header，访问的默认是正式版本：

Copy

`# curl http://springboot-rest.jadepeng.com; echo
{"hello":"ambassador"}
# curl http://springboot-rest.jadepeng.com; echo
{"hello":"ambassador"}` 

添加header，可以看到，访问的已经是灰度版本了

Copy

`# curl -H "canary: true" http://springboot-rest.jadepeng.com; echo
{"hello":"ambassador, this is a gray version"}` 

多实例Ingress controllers[#](#)
----------------------------

参考[#](#)
--------

*   [https://kubesphere.com.cn/docs/v2.0/zh-CN/quick-start/ingress-canary/#ingress-nginx-annotation-简介](https://kubesphere.com.cn/docs/v2.0/zh-CN/quick-start/ingress-canary/ target=)
*   [https://kubernetes.github.io/ingress-nginx/user-guide/nginx-configuration/annotations/#canary](https://kubernetes.github.io/ingress-nginx/user-guide/nginx-configuration/annotations/ target=)

* * *

> 作者：Jadepeng  
> 出处：jqpeng的技术记事本--[http://www.cnblogs.com/xiaoqi](http://www.cnblogs.com/xiaoqi)  
> 您的支持是对博主最大的鼓励，感谢您的认真阅读。  
> 本文版权归作者所有，欢迎转载，但未经作者同意必须保留此段声明，且在文章页面明显位置给出原文连接，否则保留追究法律责任的权利。

关注作者
----

欢迎关注作者微信公众号, 一起交流软件开发：[](https://img2020.cnblogs.com/blog/38465/202101/38465-20210118185238294-1640651808.png)[![](https://img2020.cnblogs.com/blog/38465/202101/38465-20210118185238294-1640651808.png)](https://img2020.cnblogs.com/blog/38465/202101/38465-20210118185238294-1640651808.png)

作者： JadePeng

出处：[https://www.cnblogs.com/xiaoqi/p/ingress-nginx-canary.html](https://www.cnblogs.com/xiaoqi/p/ingress-nginx-canary.html)

版权：本文采用「[署名-非商业性使用-相同方式共享 4.0 国际（欢迎转载，但未经作者同意必须保留此段声明，且在文章页面明显位置给出原文链接）](https://creativecommons.org/licenses/by-nc-sa/4.0/) 」知识共享许可协议进行许可。

分类: [java编程](https://www.cnblogs.com/xiaoqi/category/946898.html), [云原生开发](https://www.cnblogs.com/xiaoqi/category/1315234.html)

标签: [ingress-nginx](https://www.cnblogs.com/xiaoqi/tag/ingress-nginx/), [灰度发布](https://www.cnblogs.com/xiaoqi/tag/%E7%81%B0%E5%BA%A6%E5%8F%91%E5%B8%83/)

[好文要顶](#) [关注我](#) [收藏该文](#) [![](https://common.cnblogs.com/images/icon_weibo_24.png)](# "分享至新浪微博") [![](https://common.cnblogs.com/images/wechat.png)](# "分享至微信")

[![](https://pic.cnblogs.com/face/38465/20200824095212.png)](https://home.cnblogs.com/u/xiaoqi/)

[JadePeng](https://home.cnblogs.com/u/xiaoqi/)  
[关注 - 22](https://home.cnblogs.com/u/xiaoqi/followees/)  
[粉丝 - 312](https://home.cnblogs.com/u/xiaoqi/followers/)

[+加关注](#)

0

0

currentDiggType = 0;

[«](https://www.cnblogs.com/xiaoqi/p/ambassador.html) 上一篇： [基于ambassador实现K8S灰度发布](https://www.cnblogs.com/xiaoqi/p/ambassador.html "发布于 2020-01-02 20:32")  
[»](https://www.cnblogs.com/xiaoqi/p/container-jvm.html) 下一篇： [容器环境的JVM内存设置最佳实践](https://www.cnblogs.com/xiaoqi/p/container-jvm.html "发布于 2020-01-18 16:54")

posted @ 2020-01-17 16:42  [JadePeng](https://www.cnblogs.com/xiaoqi/)  阅读(6562)  评论(1)  [编辑](https://i.cnblogs.com/EditPosts.aspx?postid=12206527)  [收藏](#)