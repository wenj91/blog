# linux查看进程所在目录

## 查找进程pid
`ps -ef | grep xxx`

## 列举进程信息
`ll /proc/PID`  
cwd符号链接的是进程运行目录  
exe符号连接就是执行程序的绝对路径    
cmdline就是程序运行时输入的命令行命令  
environ记录了进程运行时的环境变量  
fd目录下是进程打开或使用的文件的符号连接   