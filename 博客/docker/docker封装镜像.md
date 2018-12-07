# docker封装镜像

## 修改容器
创建应用容器, 修改应用环境配置, 提交修改  
用到命令:
➜  ~ docker commit --help

Usage:	docker commit [OPTIONS] CONTAINER [REPOSITORY[:TAG]]

Create a new image from a container's changes

Options:
  -a, --author string    Author (e.g., "John Hannibal Smith <hannibal@a-team.com>")
  -c, --change list      Apply Dockerfile instruction to the created image
  -m, --message string   Commit message
  -p, --pause            Pause container during commit (default true)

ps:  
`docker commit -a wenj91 -c "db bak" -m "mark" db_bak mysql_bak:5.5`


## 打tag
给已经修改封装好的docker容器打tag  
用到命令:
➜  ~ docker tag --help

Usage:	docker tag SOURCE_IMAGE[:TAG] TARGET_IMAGE[:TAG]

Create a tag TARGET_IMAGE that refers to SOURCE_IMAGE  

ps:
`docker tag mysql_bak:5.5 wenj91/mysql_bak:5.5`


## 登录docker hub并上传镜像
➜  ~ docker login

➜  ~ docker push  wenj91/mysql_bak:5.5