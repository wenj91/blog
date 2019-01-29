# goland 更新dlv
在升级GO版本到1.11后发现Goland的Debug报错，如下：could not launch process: decoding dwarf section info at offset 0x0: too short。

## 原因：
* 应该是Goland的dlv不是新版本导致不能debug。

## 解决：
* 更新dlv，go get -u github.com/derekparker/delve/cmd/dlv
* 修改Goland的配置，Help->Edit Custom Properties中增加新版dlv的路径配置：dlv.path=/path/go/bin/dlv