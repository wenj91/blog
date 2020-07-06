# [Shell脚本监控Linux某个后台进程，当进程死掉后重新启动服务，以httpd为例](https://www.cnblogs.com/opsprobe/p/11725597.html)

Shell脚本如下：
vim monitor.sh

```bash
#!/bin/bash

while true   # 无限循环
flag=`ps -aux |grep "httpd" |grep -v "grep" |wc -l`
do
        if [[ $flag -eq 0 ]]   # 判断进程数如果等于0，则启动httpd
        then
                `systemctl start httpd`   # 启动httpd
                echo `date` - "Apache restart" >> running.log   # 将重启时间写入自定义的日志文件
        else
                echo "Apache is running..." >> /dev/null
        fi
        sleep 3s  # 延迟3秒后进入下次循环
done
```

运行脚本：`bash monitor.sh &`

命令末尾的 & 号，意思是将这个任务放到后台去执行。

那么如何停止脚本运行呢？

（1）首先查找运行脚本的进程PID号：

`ps -aux |grep "bash monitor.sh"`

（2）终止脚本进程：

`kill -9 进程PID号`

 

对脚本做一些说明：
ps -aux | grep    # 查找进程

参数：-aux 意思是显示所有包含其他使用者的进程。

ps -aux | grep "process_name"

若只执行这条命令，会导致出现一个 grep 进程，也就是说若只用上面的命令，会永远得到至少一个进程（grep进程），所以还需要用下面的命令，排除 grep 本身这个进程：

grep -v "grep"

最后再用 wc -l 命令统计进程数。

if 判断如果获得的进程数等于0，则证明 httpd 服务没有运行，执行启动命令。

 

sleep命令可以用来将目前动作延迟一段时间

sleep 1      延迟1秒

sleep 1s    延迟1秒

sleep 1m   延迟1分钟

sleep 1h    延迟1小时

sleep 1d    延迟1天

 