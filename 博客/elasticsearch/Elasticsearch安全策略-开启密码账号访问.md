# [Elasticsearch安全策略-开启密码账号访问](https://blog.csdn.net/qq330983778/article/details/103537252)

**关于版本**

内容

版本

Elasticsearch版本

7.2.0

Elasticsearch 启用安全策略
====================

根据官方文档，Elasticsearch 启用安全策略需要下面的步骤。

1.  验证当前版本是否支持安全功能
    
2.  是否打开安全设置
    
3.  基于FIPS的一些验证
    
4.  配置节点间通讯传输的安全性
    
5.  配置内置用户的密码
    
6.  选择用户验证用户身份的领域类型
    
7.  设置角色和用户以控制对Elasticsearch的访问
    
8.  启用审核以跟踪与Elasticsearch集群的尝试和成功的交互
    

上面是所有安全策略需要配置的内容，但是对于仅仅是启用账号密码这种最处理的安全策略我们只需要考虑下面几步。

1.  验证当前版本是否支持安全功能
2.  是否打开安全设置
3.  配置节点间通讯传输的安全性
4.  配置内置用户的密码
5.  （假如使用了kibana）修改kibana的配置

1\. 验证当前版本是否支持安全功能
------------------

Elasticsearch的安全策略需要`X-Pack`插件的支持，不过对于7.X以上版本`X-Pack`已经内置，所以不需要额外的操作。

而关于安全功能的支持，根据官方的购买说明(https://www.elastic.co/cn/subscriptions) 除了开源版之外其他版本都支持安全策略。

2\. 是否打开安全设置
------------

`xpack.security.enabled`控制安全配置的开启，在默认情况下此参数被设置为`false`。要想开启安全策略需要在**所有**集群中讲此参数进行设置

    xpack.security.enabled = true
    

3\. 配置节点间通讯传输的安全性
-----------------

仅仅开启安全设置再启动服务的时候会抛出错误

    [2]: Transport SSL must be enabled if security is enabled on a [basic] license. Please set [xpack.security.transport.ssl.enabled] to [true] or disable security by setting [xpack.security.enabled] to [false]
    

这是因为传输网络层用于集群中节点之间的内部通信。启用安全功能后，必须使用TLS来确保节点之间的通信已加密。为节点间通讯配置安全策略需要两个步骤：

1.  生成节点间安全策略使用的证书
    
2.  修改各个节点的安全配置
    

#### 3\. 1 创建证书颁发机构以及为节点生成证书

在Elasticsearch集群中验证证书真实性的推荐方法是信任签署证书的证书颁发机构（CA）。这样，将节点添加到群集后，它们只需要使用由同一CA签名的证书，即可自动允许该节点加入群集。另外证书中可以包含与节点的IP地址和DNS名称相对应的主题备用名称  
，以便可以执行主机名验证。

\*_为Elasticsearch集群创建发证机构_

使用下面的步骤为集群创建一个CA授权证书，

    bin/elasticsearch-certutil ca
    

整个创建过程是这样的。在输入命令后控制台会输出此命令的信息描述，然后你需要先执行{①}的操作然后执行{②}的操作

    This tool assists you in the generation of X.509 certificates and certificate
    signing requests for use with SSL/TLS in the Elastic stack.
    
    The 'ca' mode generates a new 'certificate authority'
    This will create a new X.509 certificate and private key that can be used
    to sign certificate when running in 'cert' mode.
    
    Use the 'ca-dn' option if you wish to configure the 'distinguished name'
    of the certificate authority
    
    By default the 'ca' mode produces a single PKCS#12 output file which holds:
        * The CA certificate
        * The CA's private key
    
    If you elect to generate PEM format certificates (the -pem option), then the output will
    be a zip file containing individual files for the CA certificate and private key
    
    Please enter the desired output file [elastic-stack-ca.p12]:  {①}
    Enter password for elastic-stack-ca.p12 : {②}
    
    

①：此位置设置文档输出地址和名称。默认名称为elastic-stack-ca.p12。这个文件是PKCS#12密钥存储库，它包含您的CA的公共证书和用于为每个节点签署证书的私有密钥。

②：此位置设置证书的密码。计划将来向集群添加更多的节点，请记住其密码。

**授权已生成**

![在这里插入图片描述](https://img-blog.csdnimg.cn/20191214114028596.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3FxMzMwOTgzNzc4,size_16,color_FFFFFF,t_70)

**为Elasticsearch集群中的节点生成证书**

使用下面的名称生成集群使用的生成节点证书。`elastic-stack-ca.p12`为上一步生成CA证书。

    bin/elasticsearch-certutil cert --ca elastic-stack-ca.p12
    

整个创建过程是这样的，类似之前的内容，在输入命令后控制台会输出此命令的信息描述，然后你需要先执行{①}的操作然后执行{②}的操作，最后执行{③}的操作

    [root@******* elasticsearch-7.2.0-a]# bin/elasticsearch-certutil cert --ca /usr/local/es-cluster/elastic-stack-ca.p12
    This tool assists you in the generation of X.509 certificates and certificate
    signing requests for use with SSL/TLS in the Elastic stack.
    
    The 'cert' mode generates X.509 certificate and private keys.
        * By default, this generates a single certificate and key for use
           on a single instance.
        * The '-multiple' option will prompt you to enter details for multiple
           instances and will generate a certificate and key for each one
        * The '-in' option allows for the certificate generation to be automated by describing
           the details of each instance in a YAML file
    
        * An instance is any piece of the Elastic Stack that requires a SSL certificate.
          Depending on your configuration, Elasticsearch, Logstash, Kibana, and Beats
          may all require a certificate and private key.
        * The minimum required value for each instance is a name. This can simply be the
          hostname, which will be used as the Common Name of the certificate. A full
          distinguished name may also be used.
        * A filename value may be required for each instance. This is necessary when the
          name would result in an invalid file or directory name. The name provided here
          is used as the directory name (within the zip) and the prefix for the key and
          certificate files. The filename is required if you are prompted and the name
          is not displayed in the prompt.
        * IP addresses and DNS names are optional. Multiple values can be specified as a
          comma separated string. If no IP addresses or DNS names are provided, you may
          disable hostname verification in your SSL configuration.
    
        * All certificates generated by this tool will be signed by a certificate authority (CA).
        * The tool can automatically generate a new CA for you, or you can provide your own with the
             -ca or -ca-cert command line options.
    
    By default the 'cert' mode produces a single PKCS#12 output file which holds:
        * The instance certificate
        * The private key for the instance certificate
        * The CA certificate
    
    If you specify any of the following options:
        * -pem (PEM formatted output)
        * -keep-ca-key (retain generated CA key)
        * -multiple (generate multiple certificates)
        * -in (generate certificates from an input file)
    then the output will be be a zip file containing individual certificate/key files
    
    Enter password for CA (/usr/local/es-cluster/elastic-stack-ca.p12) : ①
    Please enter the desired output file [elastic-certificates.p12]: ②
    Enter password for elastic-certificates.p12 : ③
    
    

`bin/elasticsearch-certutil cert --ca /usr/local/es-cluster/elastic-stack-ca.p12` 此内容为授权证书位置

① ： 此位置需要输入`elastic-stack-ca.p12` CA授权证书的密码。

② ： 此位置为需要输出证书位置。

③ ： 此位置为证书的密码。使用空密码可以直接回车结束。

默认情况下，`elasticsearch-certutil`生成的证书中没有主机名信息。这意味着可以为集群中的任意节点使用此证书，但是必须关闭主机名验证。

#### 修改每个节点的elasticsearch.yml配置

将`elastic-stack-ca.p12`文件(只需要此文件)复制到每个节点上的Elasticsearch配置目录中的一个目录中。比如我是放到了每个节点的`config/certs`目录下。

然后修改每个节点的`elasticsearch.yml`配置。添加下面的参数

    xpack.security.transport.ssl.enabled: true
    xpack.security.transport.ssl.verification_mode: certificate  
    xpack.security.transport.ssl.keystore.path: certs/elastic-certificates.p12   
    xpack.security.transport.ssl.truststore.path: certs/elastic-certificates.p12  
    

**xpack.security.transport.ssl.verification\_mode**

如果在elasticsearch-certutil cert命令中使用—dns或—ip选项，并且希望启用严格的主机名检查，此参数需要设置为`full`。而之前的例子证书中并没有输入ip以及dns等信息，所以我们没有使用严格的主机检查。

配置内置用户的密码
---------

这个时候重启集群后，如果elastic用户没有密码，则使用默认的引导密码。引导密码是一个临时密码，它允许您运行设置所有内置用户密码的工具。

我们需要为所有的内置用户设置密码。设置密码使用`bin/elasticsearch-setup-passwords interactive`命令

**密码设置过程**

    [esadmin@****** elasticsearch-7.2.0-a]$ bin/elasticsearch-setup-passwords interactive
    Initiating the setup of passwords for reserved users elastic,apm_system,kibana,logstash_system,beats_system,remote_monitoring_user.
    You will be prompted to enter passwords as the process progresses.
    Please confirm that you would like to continue [y/N]y
    
    Enter password for [elastic]: 
    Reenter password for [elastic]: 
    Enter password for [apm_system]: 
    Reenter password for [apm_system]: 
    Enter password for [kibana]: 
    Reenter password for [kibana]: 
    Enter password for [logstash_system]: 
    Reenter password for [logstash_system]: 
    Enter password for [beats_system]: 
    Reenter password for [beats_system]: 
    Enter password for [remote_monitoring_user]: 
    Reenter password for [remote_monitoring_user]: 
    Changed password for user [apm_system]
    Changed password for user [kibana]
    Changed password for user [logstash_system]
    Changed password for user [beats_system]
    Changed password for user [remote_monitoring_user]
    Changed password for user [elastic]
    [esadmin@******* elasticsearch-7.2.0-a]$ 
    
    

上面过程中我们需要设置多个默认用户的信息，每个内置用户负责不同的内容。

**内置用户**

用户名

作用

elastic

超级用户

kibana

用于负责Kibana连接Elasticsearch

logstash\_system

Logstash将监控信息存储在Elasticsearch中时使用

beats\_system

Beats在Elasticsearch中存储监视信息时使用

apm\_system

APM服务器在Elasticsearch中存储监视信息时使用

remote\_monitoring\_user

Metricbeat用户在Elasticsearch中收集和存储监视信息时使用

为elastic用户设置密码后，引导密码将不再有效。并且再次执行`elasticsearch-setup-passwords`命令会抛出异常

    Failed to authenticate user 'elastic' against http://***.***.***.***:9200/_security/_authenticate?pretty
    Possible causes include:
     * The password for the 'elastic' user has already been changed on this cluster
     * Your elasticsearch node is running against a different keystore
       This tool used the keystore at /usr/local/es-cluster/elasticsearch-7.2.0-a/config/elasticsearch.keystore
    
    ERROR: Failed to verify bootstrap password
    

修改kibana的配置
-----------

截止到目前Elasticsearch的部分已经修改完毕，下面修改kibana配置以便于让其和Elasticsearch完成连接。

**修改配置文件**

修改kibana的配置文件`config/kibana.yml`在配置文件中添加下面内容

    elasticsearch.username: "kibana"
    elasticsearch.password: "之前设置的密码"
    

**重启Kibana**

这里补充一个之前在介绍kibana没有记录的内容。

kibana 使用`ps -ef|grep kibana`是查不到进程的，因为其实运行在`node`里面。但是我们也不能关闭所有`node`里面的软件，所以我们需要查询kibana监听端口5601的进程。

使用下面命令关闭kibana

    [esadmin@****** elasticsearch-7.2.0-a]$ netstat -tunlp|grep 5601
    (Not all processes could be identified, non-owned process info
     will not be shown, you would have to be root to see it all.)
    tcp        0      0 0.0.0.0:5601            0.0.0.0:*               LISTEN      16177/bin/../node/b 
    
    [root@****** elasticsearch-7.2.0-a]# kill -9 16177
    

然后重启Kibana

    nohup ./kibana &
    

此时访问kibana（http://localhost:5601）会提示需要输入账号密码。注意此时需要输入的是elasticsearch的用户密码。

![在这里插入图片描述](https://img-blog.csdnimg.cn/20191214114059700.png?x-oss-process=image/watermark,type_ZmFuZ3poZW5naGVpdGk,shadow_10,text_aHR0cHM6Ly9ibG9nLmNzZG4ubmV0L3FxMzMwOTgzNzc4,size_16,color_FFFFFF,t_70)

到此为止ES最基础的安全策略已经添加进来了。

* * *

> 个人水平有限，上面的内容可能存在没有描述清楚或者错误的地方，假如开发同学发现了，请及时告知，我会第一时间修改相关内容。假如我的这篇内容对你有任何帮助的话，麻烦给我点一个赞。你的点赞就是我前进的动力。