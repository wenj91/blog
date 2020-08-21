# [k3s原理分析丨如何搞定k3s node注册失败问题](https://www.cnblogs.com/k3s2019/p/12422822.html)

[k3s原理分析丨如何搞定k3s node注册失败问题](https://www.cnblogs.com/k3s2019/p/12422822.html)
=============================================================================

文为实战场景中的真实故障排场案例，分享了k3s产品中关于node注册失败的排查记录。首先将分析k3s中相关组件的基本原理，然后详细分析问题出现的原因，最后给出解决方案。 如果你目前尚未遇到这个问题，也不妨一看文章中的原理部分，条分缕析地介绍了agent的注册过程，可以有效帮助你解决同类问题~~

前 言
---

面向边缘的轻量级K8S发行版k3s于去年2月底发布后，备受关注，在发布后的10个月时间里，Github Star达11,000颗。于去年11月中旬已经GA。但正如你所知，没有一个产品是十全十美的，k3s在客户落地实践的过程中也暴露过一些不足。在k3s技术团队的专业技术支持下，许多问题得到了改善和解决。

我们精选了一些在实际生产环境中的问题处理案例，分享给正在使用k3s的你。希望k3s技术团队的经验能够为你带来参考，也希望你可以参与进来和我们一起探索切磋。毕竟，寻找答案的路途永远没有终点。

本文将分享k3s产品中关于node注册失败的排查记录。

排查记录
----

### 问题描述

k3s版本：v1.17.2+k3s1

k3s agent向server注册时，日志出现明显报错：

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200305205545864.jpg)

同时，在k3s server上查询node，也确实无法获取注册的节点信息（只有一个server节点）：

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200305205614744.jpg)

客户的虚拟机环境使用某私有云，从反馈看有过VM反复清理的操作，不过具体操作无法完整复原。

### 基本原理

Agent注册的过程是十分复杂的，总的来说有两个目的：

*   启动kubelet等服务，连接到server节点上的api-server服务，这是k8s集群必须的
    
*   建立websocket tunnel，用于k3s的server和agent同步一些信息
    

我们在注册agent时只提供了server地址和node-token，agent是如何一步一步完成注册的？首先看node-token的格式：

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200305205640792.jpg)

这里的user和password会对应k3s api-server中basic auth的配置，k3s api-server启动时会设置一个特殊的authentication方式就是basic auth，对应文件在server节点的/var/lib/rancher/k3s/server/cred/passwd中：

    1a51f67d17af05b6f48357f46a9c6833,server,server,k3s:server
    0050004354d29b565f4a8bf2faba769e,admin,admin,system:masters
    1a51f67d17af05b6f48357f46a9c6833,node,node,k3s:agent
    

由此agent端通过解析node-token，可以获得一个和k3s api-server通信的授权，授权方式是basic auth。

了解node-token的作用，我们就可以解开agent注册过程的序幕，参考下图：

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200305205754888.jpg?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3FxXzQyMjA2ODEz,size_16,color_FFFFFF,t_70)

以黄色文本框顺序为例，前三步是为了得到启动kubelet服务各种依赖信息，最后一步建立websocket通道。我们可以只关心前面三步，最重要的是api-server的地址，还有各种k8s组件通信的tls证书，由于那些证书是在server上签发，所以agent需要通过一些API请求获取，这些证书大致有：

    /v1-k3s/serving-kubelet.crt
    /v1-k3s/client-kubelet.crt
    /v1-k3s/client-kube-proxy.crt
    /v1-k3s/client-k3s-controller.crt
    /v1-k3s/client-ca.crt
    /v1-k3s/server-ca.crt
    ...
    

这些证书中kubelet两个证书最为特殊，由于kubelet在每个节点都运行，所以安全需要我们需要给每个kubelet node都单独签发证书（node-name作为签发依据）。涉及到单独签发就需要验证node信息是否合法，这时node-passwd就粉墨登场了。

这个过程大致是这样的，agent先生成一个随机passwd（/etc/rancher/node/password），并把node-name和node-passwd信息作为证书请求的request header发给k3s server，由于agent会向server申请两个kubelet证书，所以会收到两个带有此header的请求。如果agent首次注册，server收到第一个请求后，会把这个node-name和node-passwd解析出来存储到/var/lib/rancher/k3s/server/cred/node-passwd中，收到第二个请求后会读取node-passwd文件与header信息校验，信息不一致则会403拒绝请求。如果agent重复注册时，server会直接比对request header内容和本地信息，信息不一致也会403拒绝请求。

### 原因分析

了解基本原理后，我们再回到问题本身，agent在注册时报出的错误日志如下：

    
    level=error msg="Node password rejected, duplicate hostname or contents of '/etc/rancher/node/password' may not match server nod
    e-passwd entry, try enabling a unique node name with the --with-node-id flag"
    

查找代码出处，确实发现这是在申请kubelet证书时，k3s server返回的403导致的：

![在这里插入图片描述](https://img-blog.csdnimg.cn/20200305210339107.jpg?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3FxXzQyMjA2ODEz,size_16,color_FFFFFF,t_70)

对比agent上的node-passwd（/etc/rancher/node/password）和server上的node-paswd：

    
    # agent
    $ cat /etc/rancher/node/password
    47211f28f469622cccf893071dbda698
    $ hostname
    xxxxxxx
    
    # server
    cat /var/lib/rancher/k3s/server/cred/node-passwd
    31567be88e5408a31cbd036fc9b37975,ip-172-31-13-54,ip-172-31-13-54,
    cf3f4f37042c05c631e07b0c0abc528f,xxxxx,xxxxxx,
    

Agent node对应的passwd和server中存储的hostname对应的passwd不一致，按照我们前面说的基本原理，就会出现403的错误日志。

### 解决方案

为什么会出现passwd不一致呢？正常来说如果用k3s-agent-uninstall.sh来清理安装过的agent node，并不会删除password文件（/etc/rancher/node/password），那么问题很可能是VM重建或者手动操作删除的这个文件。因为agent上删除了password，agent再次注册时会重新生成password，就导致了新的password和server上原先存储的不一致。

解决办法可以有三种：

*   手动在agent上创建password，内容和server中存储保持一致
    
*   修改了server中的原始内容，让password和agent上新生成的保持一致
    
*   可以试试agent注册时使用--with-node-id，这样server中认为这完全是新node，不会用原始信息比对
    

总 结
---

原则上不建议用户去触碰文中提到的这些文件，尽量把控制权交给k3s，即使我们清理agent节点，也尽量利用k3s内置的脚本。如果碰到此类问题，可以参考本文的原理介绍去分析，并通过已知的解决方案去修复它。

分类: [k3s黑魔法](https://www.cnblogs.com/k3s2019/category/1623365.html)

[好文要顶](#) [关注我](#) [收藏该文](#) [![](https://common.cnblogs.com/images/icon_weibo_24.png)](# "分享至新浪微博") [![](https://common.cnblogs.com/images/wechat.png)](# "分享至微信")

[![](https://pic.cnblogs.com/face/1909777/20191227173308.png)](https://home.cnblogs.com/u/k3s2019/)

[k3s中文社区](https://home.cnblogs.com/u/k3s2019/)  
[关注 - 0](https://home.cnblogs.com/u/k3s2019/followees/)  
[粉丝 - 6](https://home.cnblogs.com/u/k3s2019/followers/)

[+加关注](#)

1

0

currentDiggType = 0;

[«](https://www.cnblogs.com/k3s2019/p/12371184.html) 上一篇： [仅需60秒，使用k3s创建一个多节点K8S集群！](https://www.cnblogs.com/k3s2019/p/12371184.html "发布于 2020-02-27 11:33")  
[»](https://www.cnblogs.com/k3s2019/p/12484995.html) 下一篇： [IoT设备实践丨如果你也在树莓派上部署了k3s，你也许需要这篇文章](https://www.cnblogs.com/k3s2019/p/12484995.html "发布于 2020-03-13 10:52")

posted @ 2020-03-05 21:26  [k3s中文社区](https://www.cnblogs.com/k3s2019/)  阅读(464)  评论(0)  [编辑](https://i.cnblogs.com/EditPosts.aspx?postid=12422822)  [收藏](#)