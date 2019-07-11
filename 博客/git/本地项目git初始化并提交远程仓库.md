# [本地项目git初始化并提交远程仓库]
* 初始化本地仓库
git init

* 添加文件到本地仓库
git add .

* 提交文件
git commit -m "init"

* 添加远程仓库地址到本地仓库
git remote add origin {远程仓库地址}
git remote -v

git pull

git branch --set-upstream-to=origin/master master

git pull --allow-unrelated-histories

* push到远程仓库
git push -u origin master

* error
ps: Error merging: refusing to merge unrelated histories
git pull --allow-unrelated-histories