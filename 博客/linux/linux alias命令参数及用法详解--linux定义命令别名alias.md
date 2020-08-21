# [linux alias命令参数及用法详解--linux定义命令别名alias](http://www.linuxso.com/command/alias.html)

/\*360\*300，创建于2011-10-25\*/ var cpro\_id = 'u654355';

**命          令:   [alias](http://www.linuxso.com/command/alias.html)**

**功能说明：**设置指令的别名。  
  
**语　　法：**alias\[别名\]=\[指令名称\]  
  
**补充说明：**用户可利用alias，自定指令的别名。若仅输入alias，则可列出目前所有的别名设置。　alias的效力仅及于该次登入的操作。若要每次登入是即自动设好别名，可在/etc/pro[file](http://www.linuxso.com/command/file.html)或自己的~/.bashrc中设定指令的别名。

 还有，如果你想给每一位用户都生效的别名，请把alias la='[ls](http://www.linuxso.com/command/ls.html) -al' 一行加在/etc/bashrc最后面，bashrc是环境变量的配置文件 /etc/bashrc和~/.bashrc 区别就在于一个是设置给全系统一个是设置给单用户使用.

**参　　数：**若不加任何参数，则列出目前所有的别名设置。 资料来自 www.linuxso.com   Linux安全网

CentOS5.6自带的alias定义

取消别名的方法是在[命令](http://www.linuxso.com/command/)前加\\,比如 \\[mkdir](http://www.linuxso.com/command/mkdir.html)

\[root@linuxso.com ~\]#alias

alias [cp](http://www.linuxso.com/command/cp.html)\='cp -i'

alias l.='ls -d .\* --[col](http://www.linuxso.com/command/col.html)or=tty'

alias ll='ls -l --color=tty'

alias ls='ls --color=tty'

alias [mv](http://www.linuxso.com/command/mv.html)\='mv -i'

alias [rm](http://www.linuxso.com/command/rm.html)\='rm -i'

alias [which](http://www.linuxso.com/command/which.html)\='alias | /usr/bin/which --tty-only --read-alias --show-dot --show-tilde'

有的系统里没有ll这个命令,原因就是没有定义ll='ls -l --color=tty'这个别名

利用alias可以把很长的命令变成任意我们喜欢的简短的

设置和修改alias命令别名格式很简单

alias ll='ls -l --color=tty'

如果想永久生效,就把这条写入到 /etc/bashrc里面

<!-- google\_ad\_client = "ca-pub-4510523863108853"; /\* 300x250, 创建于 11-4-9 \*/ google\_ad\_slot = "4993518163"; google\_ad\_width = 300; google\_ad\_height = 250; //--> /\*336\*280，用于并排和google\*/ var cpro\_id = 'u658144';