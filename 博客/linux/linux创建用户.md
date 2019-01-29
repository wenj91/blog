# [linux创建新用户以及修改密码](https://www.cnblogs.com/30go/p/6197135.html)

1. 使用root账户创建新用户   
`useradd webuser`  
`useradd -d /root -m webuser` 
2. 修改新增的用户的密码  
`passwd webuser`
这时候会提示你输入新的密码:  

```bash
useradd -d /home/troot -m troot   
passwd  troot --输入你们的密码  
gpasswd  -a  troot   root   # 添加到组
chown -R troot:troot /data/soft/test-tomcat  
```
  
chmod 760 /home/troot  

3. 删除用户
使用root账号登录  
`userdel webuser`

## 创建系统用户：

useradd wordpress-ftp

更改所属主和所属组：

chown -R wordpress-ftp:wordpress-ftp /opt/lampp/htdocs/wordpress

创建ftp用户，注意ftp用户是虚拟用户。

/usr/local/pureftpd/bin/pure-pw useradd ftp_wordpress -u wordpress-ftp -d /opt/lampp/htdocs/wordpress

此时会出现为该ftp新用户创建密码的提示：

Password:xxxxxx
Enter it again:xxxxxx

其中，-u选项将虚拟用户ftp_wordpress与系统用户wordpress-ftp关联在一起，即使用ftp_wordpress账号登录FTP后，会以wordpress-ftp的身份来读取和下载文件，-d选项后面的目录为ftp_wordpress账户的家目录，这样可以使ftp_wordpress只能访问其家目录/opt/lampp/htdocs/wordpress

创建用户信息数据库文件：

/usr/local/pureftpd/bin/pure-pw mkdb

查看用户列表：

/usr/local/pureftpd/bin/pure-pw list

显示如下：

ftp_wordpress /opt/lampp/htdocs/wordpress/./

删除账号的命令为：

/usr/local/pureftpd/bin/pure-pw userdel ftp_wordpress

