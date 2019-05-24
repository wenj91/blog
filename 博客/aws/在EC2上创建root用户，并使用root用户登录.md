# [在EC2上创建root用户，并使用root用户登录](https://my.oschina.net/u/660932/blog/198344)

 今天开始研究亚马逊的云主机EC2，遇到了一个问题，我需要在EC2上安装tomcat，但是yum命令只能是root用户才可以运行，而EC2默认是以ec2-user用户登录的，所以需要切换到root用户登录，特将研究成果公布如下：
    1、根据官网提供的方法登录连接到EC2服务器（官网推荐windows用户使用PUTTY连接）

    2、 创建root的密码，输入如下命令：
        sudo passwd root
    3、然后会提示你输入new password。输入一个你要设置的root的密码，需要你再输入一遍进行验证。

    4、接下来，切换到root身份，输入如下命令：
        su root
    5、使用root身份编辑亚马逊云主机的ssh登录方式，找到 PasswordAuthentication no，把no改成yes。输入：
        vim /etc/ssh/sshd_config
    6、接下来，要重新启动下sshd，如下命令：
        sudo /sbin/service sshd restart
    7、然后再切换到root身份
        su root
    8、再为原来的”ec2-user”添加登录密码。如下命令：
        passwd ec2-user
    按提示，两次输入密码。到此可以用root身份直接登录EC2的服务器了。