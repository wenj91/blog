# [rclone 定时自动备份 VPS 服务器上网站数据到网盘](https://zvv.me/z/1060.html)

如今 VPS 价格基本都不贵，搭建自己的网站大多都用上 VPS 了，而数据备份这个问题也是需要关注的。大多数的廉价 VPS 服务器，本身是不会对数据丢失负责的，因此在一开始就要考虑网站数据备份的问题。

我个人来说，有一台数据量不大的服务器，直接通过 AMH 的 ambmail 扩展每日定时将数据打包，以邮件形式发送到邮箱备份。而另一台专门用于存放大文件的下载服务器，则使用了备份工具，结合 vps 定时器每日增量备份到网盘。

这里我就记录一下使用 vps 服务器工具 rclone 备份定时增量备份数据到 Google Drive 的方法。

前期准备
====

首先你得有一台 vps 服务器，安装有 Linux 或者 Windows 都可以，这里我以 Linux CentOS 7 为例。

网盘选择
----

首先对于备份来说，一个稳定的网盘尤为重要。这里我们数据并不做分享用，同时 vps 在夜间自动备份，对速度的要求也不是特别的苛刻，那么需要关注的就只有稳定和容量了。

这里常用的网盘有 [Box](https://zvv.me/go/aHR0cHM6Ly93d3cuYm94LmNvbQ==)，[Dropbox](https://zvv.me/go/aHR0cHM6Ly93d3cuZHJvcGJveC5jb20=)，[Google Drive](https://zvv.me/go/aHR0cHM6Ly9kcml2ZS5nb29nbGUuY29t)，[Hubic](https://zvv.me/go/aHR0cHM6Ly9odWJpYy5jb20v)，[OneDrive](https://zvv.me/go/aHR0cHM6Ly9vbmVkcml2ZS5saXZlLmNvbT9pbnZyZWY9ZmJiYjJiNjVlYjdhYWYwZCZpbnZzY3I9OTA=)，[Yadex Disk](https://zvv.me/go/aHR0cHM6Ly9kaXNrLnlhbmRleC5jb20vaW52aXRlLz9oYXNoPTY2TktCVEtL)。当然如果对于数据安全性更高要求，可以使用 Amazon S3，Backblaze B2 等付费空间进行备份。

我这里选择的是 Google Drive。

工具选择
----

对于 Google Drive 来说，可以使用的工具很多：[Gdrive](https://zvv.me/go/aHR0cHM6Ly9naXRodWIuY29tL3ByYXNtdXNzZW4vZ2RyaXZl)，[skicka](https://zvv.me/go/aHR0cHM6Ly9naXRodWIuY29tL2dvb2dsZS9za2lja2E=)，[google-drive-ocamlfuse](https://zvv.me/go/aHR0cHM6Ly9naXRodWIuY29tL2FzdHJhZGEvZ29vZ2xlLWRyaXZlLW9jYW1sZnVzZQ==)，[Rclone](https://zvv.me/go/aHR0cHM6Ly9naXRodWIuY29tL25jdy9yY2xvbmU=) 等。

这里由于我对备份到网盘的路径有要求，需要保持备份到 Drive 的文件相对路径与源网站一致，因此选择了 rclone 进行备份。

安装与配置备份工具 rclone
================

下载安装 rclone
-----------

rclone 官网有已经编译好的二进制文件，因此可以直接下载使用。我这里是 CentOS7，其他系统在[这里](https://zvv.me/go/aHR0cHM6Ly9yY2xvbmUub3JnL2Rvd25sb2Fkcy8=)找对应的版本

1.  wget https://downloads.rclone.org/rclone-v1.36-linux-amd64.zip
2.  unzip rclone-v1.36-linux-amd64.zip
3.  cd rclone-v1.36-linux-amd64
4.  copy rclone /usr/local/sbin/
5.  chmod +x /usr/local/sbin/rclone

这样就可以在命令行中直接输入 rclone 进行操作了

配置网盘连接
------

接下来需要连接到 Drive，进入配置交互界面：

1.  rclone config

这里的配置非常简单，根据提示进行即可。这里我们命名 Google Drive 连接名为gdrive

测试手工备份
------

配置完成后，手工备份一次，测试一下效果。这里我要将服务器/hi\_ktsee\_com/attachments/201705目录下的所有文件，备份到网盘中的/hi\_ktsee\_com/attachments/201705中，执行：

1.  rclone copy --ignore-existing /hi\_ktsee\_com/attachments/201705 gdrive:hi\_ktsee\_com/attachments/201705

这里使用了copy命令，主要是由于备份是单向的。--ignore-existing则是忽略掉网盘中已经备份过的文件，相当于增量备份了。

稍等片刻，如果没有任何错误信息返回，那么这次备份就完成了，可以在网盘中看到对应备份文件。用 rclone 备份真的的是很简单。

设置按月备份脚本
========

由于我这服务器的文件是按月归档到不同文件夹，文件夹命名格式为 "年月"，如201705，那么每个月只需要对当月目录进行增量备份即可，避免了每次备份 rclone 都要重新检查所有目录。

比如现在是 2017 年 5 月，那么今天备份脚本就应该执行：

1.  rclone copy --ignore-existing /hi\_ktsee\_com/attachments/201705 gdrive:hi\_ktsee\_com/attachments/201705

而到了 6 月，那么我希望脚本自动执行：

1.  rclone copy --ignore-existing /hi\_ktsee\_com/attachments/201706 gdrive:hi\_ktsee\_com/attachments/201706

这时可以编写一个简单的脚本，自动获取当前时间，对应不同备份指令，执行：

1.  vi sycn2gdrive.sh

写入脚本内容：

1.  #!/bin/bash
2.  cur\_month=$(date +%Y%m) && rclone copy --ignore-existing /hi\_ktsee\_com/attachments/$cur\_month gdrive:hi\_ktsee\_com/attachments/$cur\_month >> ~/sycn2gdrive\_$cur\_month.log

这里定义了一个变量 cur\_month 获取当前时间，组合成我们需要的目录形式201705，接着调用备份命令对指定目录备份，最后输出到 log 文件，以便于备份出错时查看错误记录。

设置系统定时器（计划任务）
=============

定时执行脚本有几种方案，包括 AT，crontab 和 systemd.timer。

其中 AT 常用于只执行一次的任务，虽然结合守护进程 atd 也可以实现定时效果。crontab 之前非常常用，是不错的选择，但是这里对于 CentOS7 上新的 systemd 的用法，还是要学习一下，因此这次使用了 systemd.timer 定时器。

配置定时器
-----

首先进入 systemd 服务文件存放目录，新建一个sync2gdrive.service文件：

1.  cd /etc/systemd/system
2.  vi sync2gdrive.service

填入内容：

1.  \[Unit\]
2.  Description=Sync local files to google drive

4.  \[Service\]
5.  Type=simple
6.  ExecStart=/root/sync2gdrive.sh

这里/root/sync2gdrive.sh是上一步编写的脚本的路径

然后新建sync2gdrive.timer文件：

1.  vi sync2gdrive.timer

填入内容：

1.  \[Unit\]
2.  Description=Daily sync local files to google drive
3.  \[Timer\]
4.  OnBootSec=5min
5.  OnUnitActiveSec=1d
6.  TimeoutStartSec=1h
7.  Unit=sync2gdrive.service

9.  \[Install\]
10.  WantedBy=multi-user.target

*   OnBootSec表示开机后五分钟后启动
*   OnUnitActiveSec表示每隔 1 天执行一次
*   TimeoutStartSec表示脚本执行后 1 小时后检查结果，防止备份时间过长，脚本认为没有响应，认为任务失败而中断任务

启用定时器并加入开机启动
------------

这里启用以及加入开机启动，就与其他服务一样，只是注意末位是timer：

1.  systemctl enable sync2gdrive.timer
2.  systemctl start sync2gdrive.timer

这样自动备份就基本配置完成了。

via。http://hi.ktsee.com/646.html

最后修改：2017 年 07 月 04 日 06 : 21 AM

© 允许规范转载