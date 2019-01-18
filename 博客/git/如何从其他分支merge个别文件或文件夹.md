# [如何从其他分支merge个别文件或文件夹](https://segmentfault.com/a/1190000008360855)

## 合并分支
使用git merge 命令进行分支合并是通用的做法，但是git merge 合并的时候会将两个分支的内容完全合并，如果想合并一部分肯定是不行的。那怎么办？

如何从其他分支merge指定文件到当前分支，git checkout 是个合适的工具。
```
git checkout source_branch <path>...
```

## 强制合并
我们使用git checkout 将B分支上的系统消息功能添加到A分支上
```
$ git branch
  * A  
    B
    
$ git checkout B message.html message.css message.js other.js

$ git status
# On branch A
# Changes to be committed:
#   (use "git reset HEAD <file>..." to unstage)
#
#    new file:   message.css
#    new file:   message.html
#    new file:   message.js
#    modified:   other.js
#
```
合并完成

注意：在使用git checkout某文件到当前分支时，会将当前分支的对应文件强行覆盖

这里新增文件没问题，但是A分支上原有的other.js会被强行覆盖，如果A分支上的other.js有修改，在checkout的时候就会将other.js内容强行覆盖，这样肯定是不行的。如何避免不强制覆盖，往下看。


## 智能合并
1.使用git checkout 将根据A分支创建一个A_temp分支，避免影响A分支
```
$ git checkout -b A_temp
Switched to a new branch 'A_temp'
```
2.将B分支合并到A_temp分支

```
$ git merge B
Updating 1f73596..04627b5
Fast-forward
 message.css                     | 0
 message.html                    | 0
 message.js                      | 0
 other.js                        | 1 +
 4 files changed, 1 insertion(+)
 create mode 100644 message.css
 create mode 100644 message.html
 create mode 100644 message.js
```
3.切换到A分支，并使用git checkout 将A_temp分支上的系统消息功能相关文件或文件夹覆盖到A分支

```
$ git checkout A
Switched to branch 'A'

$ git checkout A_temp message.html message.css message.js other.js

$ git status
# On branch A
# Changes to be committed:
#   (use "git reset HEAD <file>..." to unstage)
#
#    new file:   message.css
#    new file:   message.html
#    new file:   message.js
#    modified:   other.js
#
```