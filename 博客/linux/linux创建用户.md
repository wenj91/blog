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

