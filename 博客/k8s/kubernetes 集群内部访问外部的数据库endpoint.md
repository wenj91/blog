# [kubernetes 集群内部访问外部的数据库endpoint](https://www.cnblogs.com/kuku0223/p/10898068.html)

[kubernetes 集群内部访问外部的数据库endpoint](https://www.cnblogs.com/kuku0223/p/10898068.html)
===================================================================================

#### k8s访问集群外独立的服务最好的方式是采用Endpoint方式，以mysql服务为例：

*   创建mysql-service.yaml

[?](#)

1

2

3

4

5

6

7

`apiVersion: v1`

`kind: Service`

`metadata:`

 `name: mysql-production`

`spec:`

 `ports:`

 `- port: 3306`

*   创建mysql-endpoints.yaml

[?](#)

1

2

3

4

5

6

7

8

9

10

`kind: Endpoints`

`apiVersion: v1`

`metadata:`

 `name: mysql-production`

 `namespace: default`

`subsets:`

 `- addresses:`

 `- ip: 192.168.1.25`

 `ports:`

 `- port: 3306`

就是将外部IP地址和服务引入到k8s集群内部，由service作为一个代理来达到能够访问外部服务的目的。

分类: [kubernetes](https://www.cnblogs.com/kuku0223/category/1220945.html)

[好文要顶](#) [关注我](#) [收藏该文](#) [![](https://common.cnblogs.com/images/icon_weibo_24.png)](# "分享至新浪微博") [![](https://common.cnblogs.com/images/wechat.png)](# "分享至微信")

[![](https://pic.cnblogs.com/face/sample_face.gif)](https://home.cnblogs.com/u/kuku0223/)

[划得戳](https://home.cnblogs.com/u/kuku0223/)  
[关注 - 1](https://home.cnblogs.com/u/kuku0223/followees/)  
[粉丝 - 12](https://home.cnblogs.com/u/kuku0223/followers/)

[+加关注](#)

0

0

currentDiggType = 0;

[«](https://www.cnblogs.com/kuku0223/p/10782149.html) 上一篇： [web压力测试工具](https://www.cnblogs.com/kuku0223/p/10782149.html "发布于 2019-04-28 09:33")  
[»](https://www.cnblogs.com/kuku0223/p/10906003.html) 下一篇： [kubernetes 实现redis-statefulset集群](https://www.cnblogs.com/kuku0223/p/10906003.html "发布于 2019-05-22 15:06")

posted @ 2019-05-21 09:56  [划得戳](https://www.cnblogs.com/kuku0223/)  阅读(1645)  评论(0)  [编辑](https://i.cnblogs.com/EditPosts.aspx?postid=10898068)  [收藏](#)