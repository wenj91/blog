# linux清除记录
echo > /var/log/wtmp //此文件默认打开时乱码，可查到ip等信息
last //此时即查不到用户登录信息

echo > /var/log/btmp //此文件默认打开时乱码，可查到登陆失败信息
lastb //查不到登陆失败信息

history -c //清空历史执行命令
echo > ./.bash_history //或清空用户目录下的这个文件即可
vi /root/history //新建记录文件
history -c //清除记录 
history -r /root/history.txt //导入记录 
history //查询导入结果

vi /root/history
history -c 
history -r /root/history.txt 
history 

echo > /var/log/wtmp  
last
echo > /var/log/btmp
lastb 

history -c 
echo > ./.bash_history
history

echo 0>/var/spool/mail/root
echo 0>/var/log/wtmp
echo 0>/var/log/secure
echo 0>/var/log/cron
echo 0>/var/log/btmp