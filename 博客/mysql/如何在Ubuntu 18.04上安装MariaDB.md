# [如何在Ubuntu 18.04上安装MariaDB](https://www.myfreax.com/how-to-install-mariadb-on-ubuntu-18-04/)

MariaDB是一个开放源代码，多线程关系数据库管理系统，是MySQL的向后兼容替代品。它由 MariaDB Foundation 进行维护和开发，其中包括MySQL的某些原始开发人员。

在本教程中，我们将向您展示两种如何在Ubuntu 18.04计算机上安装MariaDB的方法。第一种方法描述了从Ubuntu存储库安装MariaDB所需的步骤，第二种方法将向您展示如何从官方MariaDB存储库安装最新版本的MariaDB。

通常，建议使用第一种方法并安装Ubuntu提供的MariaDB软件包。

如果要安装MySQL而不是MariaDB，请查看如何在Ubuntu 18.04上安装MySQL 教程。

先决条件
在继续学习本教程之前，请确保您以个具有sudo特权的用户身份登录。

在Ubuntu 18.04上安装MariaDB
在撰写本文时，Ubuntu主存储库中包含MariaDB版本10.1。

要在Ubuntu 18.04上安装MariaDB，请按照以下步骤操作：

更新软件包索引。

sudo apt update
Copy
更新软件包列表后，请通过以下方式安装MariaDB：发出以下命令：

sudo apt install mariadb-server
Copy
MariaDB服务将自动启动。您可以通过键入以下内容进行验证：

sudo systemctl status mariadb
Copy
    ● mariadb.service - MariaDB database server
Loaded: loaded (/lib/systemd/system/mariadb.service; enabled; vendor preset
Active: active (running) since Sun 2018-07-29 19:31:31 UTC; 38s ago
Main PID: 13932 (mysqld)
Status: "Taking your SQL requests now..."
    Tasks: 27 (limit: 507)
CGroup: /system.slice/mariadb.service
        └─13932 /usr/sbin/mysqld
Copy
您还可以检查MariaDB版本与：

mysql -V
Copy
mysql  Ver 15.1 Distrib 10.1.29-MariaDB, for debian-linux-gnu (x86_64) using readline 5.2
Copy
从MariaDB存储库在Ubuntu 18.04上安装MariaDB
在撰写本文时，可从官方MariaDB存储库中获得的MariaDB最新版本是MariaDB版本10.3。在继续下一步之前，您应该访问 MariaDB存储库页面，并检查是否有可用的新版本。

要在Ubuntu 18.04服务器上安装MariaDB 10.3，请执行以下步骤：

首先使用以下命令将MariaDB GPG密钥添加到您的系统：

sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
Copy
导入密钥后，在MariaDB存储库中添加以下内容：

sudo add-apt-repository 'deb [arch=amd64,arm64,ppc64el] http://ftp.utexas.edu/mariadb/repo/10.3/ubuntu bionic main'
Copy
如果收到错误消息，提示add-apt-repository command not found安装software-properties-common软件包。

要能够从MariaDB存储库安装软件包，您需要更新软件包列表：

sudo apt update
Copy
现在添加了存储库，安装带有以下内容的MariaDB软件包：

sudo apt install mariadb-server
Copy
MariaDB服务将自动启动，以验证其类型：

sudo systemctl status mariadb
Copy
● mariadb.service - MariaDB 10.3.8 database server
Loaded: loaded (/lib/systemd/system/mariadb.service; enabled; vendor preset: enabled)
Drop-In: /etc/systemd/system/mariadb.service.d
        └─migrated-from-my.cnf-settings.conf
Active: active (running) since Sun 2018-07-29 19:36:30 UTC; 56s ago
    Docs: man:mysqld(8)
        https://mariadb.com/kb/en/library/systemd/
Main PID: 16417 (mysqld)
Status: "Taking your SQL requests now..."
    Tasks: 31 (limit: 507)
CGroup: /system.slice/mariadb.service
        └─16417 /usr/sbin/mysqld
Copy
并打印出MariaDB服务器版本，其中包括：

mysql -V
Copy
mysql  Ver 15.1 Distrib 10.3.8-MariaDB, for debian-linux-gnu (x86_64) using readline 5.2
Copy
保护MariaDB
运行mysql_secure_installation命令以提高MariaDB安装的安全性：

sudo mysql_secure_installation
Copy
该脚本将提示您设置root用户密码，删除匿名用户，限制root用户对本地计算机的访问并删除测试数据库。最后，脚本将重新加载特权表，以确保所有更改立即生效。

详细说明了所有步骤，建议对所有问题回答“是”（是）。

从命令行连接到MariaDB
要通过终端连接到MariaDB服务器，我们可以使用MariaDB客户端。

要以root用户身份登录MariaDB服务器，请输入：

mysql -u root -p
Copy
运行mysql_secure_installation脚本时，系统会提示您输入先前设置的root密码。

输入密码后，MariaDB shell将会显示，如下所示：

Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is 49
Server version: 10.1.29-MariaDB-6 Ubuntu 18.04

Copyright (c) 2000, 2017, Oracle, MariaDB Corporation Ab and others.

Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
Copy
结论
现在您的MariaDB服务器已启动并正在运行，并且您知道如何从命令行连接到MariaDB服务器，您可能需要查看以下指南：

如何管理MySQL用户帐户和数据库
如何重设MySQL根密码
如何创建MySQL数据库
如何创建MySQL用户帐户和授予特权
如何显示MySQL用户
如何使用Mysqldump备份和还原MySQL数据库
如果您希望通过命令行使用Web界面，则可以安装phpMyAdmin 并通过它管理MariaDB数据库和用户

如果你喜欢我们的内容可以选择在下方二维码中捐赠我们，或者点击广告予以支持，感谢你的支持