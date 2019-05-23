# [Too many authentication failures for root 问题解决办法](https://www.cnblogs.com/chunguang/p/5553111.html)

## 
```bash
vim /etc/ssh/sshd_config  最后参数
UseDNS no
AddressFamily inet
PermitRootLogin yes
SyslogFacility AUTHPRIV
PasswordAuthentication yes
MaxAuthTries=2    把这里改大一点即可。
```