# mac本身安装了ssh服务，默认情况下不会开机自启

## 1.启动sshd服务：
sudo launchctl load -w /System/Library/LaunchDaemons/ssh.plist

## 2.停止sshd服务：
sudo launchctl unload -w /System/Library/LaunchDaemons/ssh.plist

## 3查看是否启动：
sudo launchctl list | grep ssh

如果看到下面的输出表示成功启动了：
－－－－－－－－－－－－－－
- 0 com.openssh.sshd