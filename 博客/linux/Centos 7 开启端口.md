# centos7 开启端口

## 查询是否开启xx端口
`firewall-cmd --query-port=xx/tcp`
如果命令结果返回no则没有开启
返回yes则已经开启 

## 开启xx端口
`firewall-cmd --add-port=xx/tcp`
返回success则开启成功