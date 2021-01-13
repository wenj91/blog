# [删除GitHub上的Tag](https://blog.csdn.net/x356982611/article/details/93206444)

有时候需要删除GitHub上打的tag，GitHub删除tag需要在命令行操作

```bash
git tag -d [tag];
git push origin :[tag]
```

删除示例，可以看到界面上的tag已经删除

```bash
$ git tag -d 3.3.0.1492
Deleted tag '3.3.0.1492' (was f74dcae)

$ git push origin :3.3.0.1492
To https://github.com/liulangwa/golang-sonar-scanner.git
 - [deleted]         3.3.0.1492
```

如果分支和tag重名了，可以 用下面的方法删除

```bash
git tag -d [tag] 
git push origin：refs / tags / [tag]
```
