# win10卸载子系统

If your OS has been upgraded to the Fall Creators Update, you should be able to issue the command wslconfig.

You could of course try uninstalling first by using the command lxrun /uninstall /full.

If its still there, you can try unregistering the distro:

First you need to know which distro is installed by using

wslconfig /l
From the list choose the distro (e.g. Ubuntu) you want to uninstall and type the command

wslconfig /u Ubuntu