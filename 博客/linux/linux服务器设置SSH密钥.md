# [教程][如何在vultr服务器CentOS 7上设置SSH密钥](http://www.aiwanba.net/post/1092.html)

基于SSH密钥的认证为基于密码的认证提供了更安全的选择。在本教程中，我们将学习如何在CentOS 7安装上设置基于SSH密钥的身份验证。

## 介绍
SSH或安全外壳是用于管理和与服务器通信的加密协议。 在使用CentOS服务器时，很可能你会花大部分时间在通过SSH连接到服务器的终端会话中。

在本指南中，我们将重点介绍为香草CentOS 7安装设置SSH密钥。 SSH密钥提供了一种直接，安全的登录服务器的方式，并建议所有用户使用。

## 第1步 - 创建RSA密钥对
第一步是在客户机（通常是您的计算机）上创建密钥对：

ssh-keygen  
默认情况下， ssh-keygen将创建一个2048位的RSA密钥对，对于大多数使用情况（您可以选择传入-b 4096标志以创建更大的4096位密钥）足够安全。

输入命令后，您应该看到以下提示：

OutputEnter file in which to save the key (/your_home/.ssh/id_rsa):
按ENTER将密钥对保存到您的主目录中的.ssh/子目录中，或指定备用路径。

您应该看到以下提示：

OutputEnter passphrase (empty for no passphrase):
在这里你可以选择输入一个安全的密码，这是强烈推荐的。 密码会增加额外的安全层以防止未经授权的用户登录。要了解有关安全性的更多信息，请参阅我们的关于如何在Linux服务器上配置基于SSH密钥的身份验证的教程。

您应该看到以下输出：
```bash
OutputYour identification has been saved in /your_home/.ssh/id_rsa.
Your public key has been saved in /your_home/.ssh/id_rsa.pub.
The key fingerprint is:
a9:49:2e:2a:5e:33:3e:a9:de:4e:77:11:58:b6:90:26 username@remote_host
The key's randomart image is:
+--[ RSA 2048]----+
|     ..o         |
|   E o= .        |
|    o. o         |
|        ..       |
|      ..S        |
|     o o.        |
|   =o.+.         |
|. =++..          |
|o=++.            |
+-----------------+
```
您现在拥有可用于验证的公钥和私钥。 下一步是将公钥放在服务器上，以便您可以使用基于SSH密钥的身份验证来登录。

## 第2步 - 将公钥复制到CentOS服务器
将您的公钥复制到CentOS主机的最快方法是使用名为ssh-copy-id的实用程序。 由于其简单性，如果可用，强烈建议使用此方法。 如果您的客户机上没有可用的ssh-copy-id ，则可以使用本节提供的两种备用方法之一（通过基于密码的SSH复制或手动复制密钥）。

### 使用ssh-copy-id复制您的公钥
ssh-copy-id工具默认包含在许多操作系统中，因此您可以在本地系统上使用它。 要使此方法起作用，您必须已经拥有基于密码的SSH访问服务器的权限。

要使用该实用程序，只需指定要连接到的远程主机以及具有SSH访问密码的用户帐户。 这是您的公用SSH密钥将被复制到的帐户。

语法是：

ssh-copy-id username@remote_host
您可能会看到以下消息：
```bash
OutputThe authenticity of host '111.111.11.111 (111.111.11.111)' can't be established.
ECDSA key fingerprint is fd:fd:d4:f9:77:fe:73:84:e1:55:00:ad:d6:6d:22:fe.
Are you sure you want to continue connecting (yes/no)? yes
```
这仅表示您的本地计算机无法识别远程主机。 这将在您第一次连接到新主机时发生。 键入“是”并按ENTER继续。

接下来，该实用程序将扫描您本地帐户以查找我们之前创建的id_rsa.pub密钥。 当它找到密钥时，它会提示您输入远程用户帐户的密码：
```bash
Output/usr/bin/ssh-copy-id: INFO: attempting to log in with the new key(s), to filter out any that are already installed
/usr/bin/ssh-copy-id: INFO: 1 key(s) remain to be installed -- if you are prompted now it is to install the new keys
username@111.111.11.111's password:
```
输入密码（出于安全目的，您的输入不会显示），然后按ENTER 。 该实用程序将使用您提供的密码连接到远程主机上的帐户。 然后它会将你的~/.ssh/id_rsa.pub密钥的内容~/.ssh/id_rsa.pub到远程帐户的~/.ssh目录下名为authorized_keys 。

您应该看到以下输出：
```bash
OutputNumber of key(s) added: 1

Now try logging into the machine, with:   "ssh 'username@111.111.11.111'"
and check to make sure that only the key(s) you wanted were added.
```
此时，您的id_rsa.pub密钥已上传到远程帐户。 您可以继续第3步 。

### 使用SSH复制公钥
如果您没有ssh-copy-id可用，但您可以通过基于密码的SSH访问服务器上的帐户，则可以使用传统的SSH方法上传密钥。

我们可以通过使用cat命令读取本地计算机上的公用SSH密钥的内容并通过SSH连接到远程服务器来实现此目的。 另一方面，我们可以确保~/.ssh目录存在于我们正在使用的帐户下，然后将我们传送的内容输出到该目录中名为authorized_keys的文件中。

我们将使用>>重定向符号追加内容而不是覆盖它。 这将让我们添加密钥而不破坏以前添加的密钥。

完整的命令如下所示：
```bash
cat ~/.ssh/id_rsa.pub | ssh username@remote_host "mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys"
```
您可能会看到以下消息：
```bash
OutputThe authenticity of host '111.111.11.111 (111.111.11.111)' can't be established.
ECDSA key fingerprint is fd:fd:d4:f9:77:fe:73:84:e1:55:00:ad:d6:6d:22:fe.
Are you sure you want to continue connecting (yes/no)? yes
```
这意味着您的本地计算机无法识别远程主机。 这将在您第一次连接到新主机时发生。 键入“是”并按ENTER继续。

之后，系统会提示您输入远程用户帐户密码：
```bash
Outputusername@111.111.11.111's password:
```
输入密码后（您的输入将不会显示），您的id_rsa.pub密钥的内容将被复制到远程用户帐户的authorized_keys文件的末尾。

完成本节后，继续执行第3步 。

### 手动复制公钥
如果您没有可用的基于密码的SSH访问服务器，则必须手动复制公钥。

在本节中，我们将手动将id_rsa.pub文件的内容附加到远程机器上的~/.ssh/authorized_keys文件中。

要显示id_rsa.pub键的内容，请在本地计算机中键入以下命令：

`cat ~/.ssh/id_rsa.pub`  
你会看到钥匙的内容，应该是这样的：
```bash
Outputssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQCqql6MzstZYh1TmWWv11q5O3pISj2ZFl9HgH1JLknLLx44+tXfJ7mIrKNxOOwxIxvcBF8PXSYvobFYEZjGIVCEAjrUzLiIxbyCoxVyle7Q+bqgZ8SeeM8wzytsY+dVGcBxF6N4JS+zVk5eMcV385gG3Y6ON3EG112n6d+SMXY0OEBIcO6x+PnUSGHrSgpBgX7Ks1r7xqFa7heJLLt2wWwkARptX7udSq05paBhcpB0pHtA1Rfz3K2B+ZVIpSDfki9UVKzT8JUmwW6NNzSgxUfQHGwnW7kj4jp4AT0VZk3ADw497M2G/12N0PPB5CnhHf7ovgy6nL1ikrygTKRFmNZISvAcywB9GVqNAVE+ZHDSCuURNsAInVzgYo9xgJDW8wUw2o8U77+xiFxgI5QSZX3Iq7YLMgeksaO4rBJEa54k8m5wEiEE1nUhLuJ0X/vh2xPff6SQ1BL/zkOhvJCACK6Vb15mDOeCSq54Cr7kvS46itMosi/uS66+PujOO+xt/2FWYepz6ZlN70bRly57Q06J+ZJoc9FfBCbCyYH7U/ASsmY095ywPsBo1XQ9PqhnN1/YOorJ068foQDNVpm146mUpILVxmq41Cj55YKHEazXGsdBIbXWhcrRf4G2fJLRcGUr9q8/lERo9oxRm5JFX6TCmj6kmiFqv+Ow9gI0x8GvaQ== demo@test
```
使用可用的方法访问您的远程主机。

一旦你有权访问远程服务器上的帐户，你应该确保存在~/.ssh目录。 如果需要，该命令将创建该目录，或者如果已经存在，则不执行任何操作：

```bash
mkdir -p ~/.ssh
```
现在，您可以在此目录中创建或修改authorized_keys文件。 您可以将id_rsa.pub文件的内容添加到id_rsa.pub文件的末尾，并根据需要使用以下命令创建它：
```bash
echo public_key_string >> ~/.ssh/authorized_keys
```
在上面的命令中，将public_key_string替换为您在本地系统上执行的public_key_string cat ~/.ssh/id_rsa.pub命令的输出。 它应该以ssh-rsa AAAA...开头。

我们现在可以尝试使用我们的CentOS服务器进行无密码验证。

## 第3步 - 使用SSH密钥验证您的CentOS服务器
如果您已成功完成上述过程之一，则应能够在没有远程帐户密码的情况下登录到远程主机。

基本过程是一样的：

`ssh username@remote_host`  
如果这是您第一次连接到此主机（如果您使用上面的最后一种方法），则可能会看到类似下面的内容：

```bash
OutputThe authenticity of host '111.111.11.111 (111.111.11.111)' can't be established.
ECDSA key fingerprint is fd:fd:d4:f9:77:fe:73:84:e1:55:00:ad:d6:6d:22:fe.
Are you sure you want to continue connecting (yes/no)? yes
```
这意味着您的本地计算机无法识别远程主机。 键入“是”，然后按ENTER继续。

如果您没有为您的私钥提供密码，您将立即登录。 如果您在创建密钥时为密钥提供了密码，则系统会提示您现在输入密码。 在进行身份验证之后，应使用CentOS服务器上的已配置帐户为您打开新的shell会话。

如果基于密钥的身份验证成功，请继续学习如何通过禁用密码身份验证来进一步保护您的系统。

## 第4步 - 在服务器上禁用密码验证
如果您无需密码就可以使用SSH登录到您的帐户，则您已成功为您的帐户配置基于SSH密钥的身份验证。 但是，您的基于密码的身份验证机制仍然处于活动状态，这意味着您的服务器仍然受到暴力攻击。

在完成本节中的步骤之前，请确保您为此服务器上的根帐户配置了基于SSH密钥的身份验证，或者最好是在此基础上为非root帐户配置基于SSH密钥的身份验证具有sudo权限的服务器。 这一步将锁定基于密码的登录，因此确保您仍然能够获得管理访问至关重要。

确认您的远程帐户具有管理权限后，请使用SSH密钥登录您的远程服务器，无论是以root身份还是使用具有sudo权限的帐户登录。 然后，打开SSH守护进程的配置文件：

`sudo vi /etc/ssh/sshd_config`  
在文件内部，搜索一个名为PasswordAuthentication的指令。 这可能会被注释掉。 按i插入文本，然后取消注释并将值设置为“否”。 这将禁用您使用帐户密码通过SSH登录的功能：

的/ etc / SSH / sshd_config中
...
PasswordAuthentication no
...
完成更改后，按ESC然后:wq将更改写入文件并退出。 要真正实现这些更改，我们需要重新启动sshd服务：

sudo systemctl restart sshd.service
作为预防措施，打开一个新的终端窗口并在关闭此会话之前测试SSH服务是否正常运行：

ssh username@remote_host
一旦你验证了你的SSH服务，你可以安全地关闭所有当前的服务器会话。

现在CentOS服务器上的SSH守护进程只响应SSH密钥。 基于密码的身份验证已成功禁用。

结论
您现在应该在服务器上配置基于SSH密钥的身份验证，允许您在不提供帐户密码的情况下登录。

如果您想了解有关使用SSH的更多信息，请参阅我们的SSH Essentials Guide 