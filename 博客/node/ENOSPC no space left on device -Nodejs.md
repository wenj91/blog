# [ENOSPC no space left on device -Nodejs](https://stackoverflow.com/questions/50142049/enospc-no-space-left-on-device-nodejs)

35

I had the same problem, take a look at the selected answer in the Stackoverflow here:

Node.JS Error: ENOSPC

Here is the command that I used (my OS: LinuxMint 18.3 Sylvia which is a Ubuntu/Debian based Linux system).

```bash
echo fs.inotify.max_user_watches=524288 | sudo tee -a /etc/sysctl.conf && sudo sysctl -p
```

