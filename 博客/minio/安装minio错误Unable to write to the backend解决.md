# [安装minio错误Unable to write to the backend解决](https://github.com/minio/minio/issues/6237)

[Jump to bottom](#)

Can't mount host data directory to minio server docker container #6237
======================================================================

Closed

[riazarbi](https://github.com/riazarbi) opened this issue on 3 Aug 2018 · 5 comments

Closed

[Can't mount host data directory to minio server docker container](#) #6237
===========================================================================

[riazarbi](https://github.com/riazarbi) opened this issue on 3 Aug 2018 · 5 comments

Comments
--------

[![@riazarbi](https://avatars3.githubusercontent.com/u/13433911?s=88&v=4)](https://github.com/riazarbi)

Copy link Quote reply

### [![@riazarbi](https://avatars2.githubusercontent.com/u/13433911?s=60&v=4)](https://github.com/riazarbi) ** [riazarbi](https://github.com/riazarbi) ** commented [on 3 Aug 2018](#) 

Expected Behavior
-----------------

Add the option `-v /mnt/data:/data` to the `docker run` command to have minio saving data to host directory.

Current Behavior
----------------

Minio cannot write to data directory because of permission error.

    Created minio configuration file successfully at /root/.minio                                                                                                                                                 
    ERROR Unable to initialize backend: Unable to write to the backend.                                                                                                                                           
          > Please ensure Minio binary has write permissions for the backend.
    

Possible Solution
-----------------

I am probably doing something stupid; solution is to point out the obvious thing I'm missing.

Steps to Reproduce (for bugs)
-----------------------------

Starting with the simplest case.  
Create folder on host where data will reside:

    sudo su
    mkdir /mnt/data
    chmod -R 777 /mnt/data
    

Run a new docker container:

    docker run -p 9000:9000 --name minio1 \
      -v /mnt/data:/data \
      minio/minio server /data
    

Result:

    Created minio configuration file successfully at /root/.minio                                                                                                                                                 
    ERROR Unable to initialize backend: Unable to write to the backend.                                                                                                                                           
          > Please ensure Minio binary has write permissions for the backend.
    

Context
-------

I just want to move from the super-toy example of spinning up a minio docker container to the somewhat toy example of binding the minio /data directory to the host /mnt/data directory.

Your Environment
----------------

*   Version used : minio/minio:latest docker image
*   Docker host: `centos-atomic-host 7.1805`

[![@riazarbi](https://avatars3.githubusercontent.com/u/13433911?s=88&v=4)](https://github.com/riazarbi)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

It was a silly issue; posting solution for posterity. Key issue was this: Docker host: \`centos-atomic-host 7.1805\` Atomic host runs SELinux by default. Turning off SELinux resolves the issue. \_Originally posted by @riazarbi in https://github.com/minio/minio/issues/6237#issuecomment-410181646\_

Create issue

Author

### [![@riazarbi](https://avatars2.githubusercontent.com/u/13433911?s=60&v=4)](https://github.com/riazarbi) ** [riazarbi](https://github.com/riazarbi) ** commented [on 3 Aug 2018](#) 

It was a silly issue; posting solution for posterity. Key issue was this:

Docker host: `centos-atomic-host 7.1805`

Atomic host runs SELinux by default. Turning off SELinux resolves the issue.

[![@riazarbi](https://avatars2.githubusercontent.com/u/13433911?s=60&v=4)](https://github.com/riazarbi) [riazarbi](https://github.com/riazarbi) closed this [on 3 Aug 2018](#)

[![@harshavardhana](https://avatars0.githubusercontent.com/u/622699?s=88&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

\> Atomic host runs SELinux by default. Turning off SELinux resolves the issue. Do you have any docs which allow us to provide SELinux make this work? \_Originally posted by @harshavardhana in https://github.com/minio/minio/issues/6237#issuecomment-410318386\_

Create issue

Member

### [![@harshavardhana](https://avatars3.githubusercontent.com/u/622699?s=60&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana) ** [harshavardhana](https://github.com/harshavardhana) ** commented [on 4 Aug 2018](#) 

> Atomic host runs SELinux by default. Turning off SELinux resolves the issue.

Do you have any docs which allow us to provide SELinux make this work?

[![@eco-minio](https://avatars2.githubusercontent.com/u/41090896?s=88&u=6ca0913bf6cc17d4cb0c71499a68e3989ace8744&v=4)](https://github.com/eco-minio)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

For Project Atomic you can also set append :z or :Z to the have SElinux allow writing to the container: docker run -p 9000:9000 --name minio1 \\ -v /mnt/data:/data:z \\ minio/minio server /data More info at: https://www.projectatomic.io/blog/2016/03/dwalsh\_selinux\_containers/ \_Originally posted by @eco-minio in https://github.com/minio/minio/issues/6237#issuecomment-410351496\_

Create issue

Contributor

### [![@eco-minio](https://avatars1.githubusercontent.com/u/41090896?s=60&u=6ca0913bf6cc17d4cb0c71499a68e3989ace8744&v=4)](https://github.com/eco-minio) ** [eco-minio](https://github.com/eco-minio) ** commented [on 4 Aug 2018](#) 

For Project Atomic you can also set append :z or :Z to the have SElinux allow writing to the container:

docker run -p 9000:9000 --name minio1  
\-v /mnt/data:/data:z  
minio/minio server /data

More info at:  
[https://www.projectatomic.io/blog/2016/03/dwalsh\_selinux\_containers/](https://www.projectatomic.io/blog/2016/03/dwalsh_selinux_containers/)

 

👍 6

[![@riazarbi](https://avatars3.githubusercontent.com/u/13433911?s=88&v=4)](https://github.com/riazarbi)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

@eco-minio command works. Thanks! \_Originally posted by @riazarbi in https://github.com/minio/minio/issues/6237#issuecomment-410987780\_

Create issue

Author

### [![@riazarbi](https://avatars2.githubusercontent.com/u/13433911?s=60&v=4)](https://github.com/riazarbi) ** [riazarbi](https://github.com/riazarbi) ** commented [on 7 Aug 2018](#) 

[@eco-minio](https://github.com/eco-minio) command works. Thanks!

[![@lock](https://avatars1.githubusercontent.com/in/6672?s=88&v=4)](https://github.com/apps/lock)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

This thread has been automatically locked since there has not been any recent activity after it was closed. Please open a new issue for related bugs. \_Originally posted by @lock in https://github.com/minio/minio/issues/6237#issuecomment-619359732\_

Create issue

### [![@lock](https://avatars0.githubusercontent.com/in/6672?s=60&v=4)](https://github.com/apps/lock) ** [lock](https://github.com/apps/lock) bot ** commented [on 25 Apr](#) 

This thread has been automatically locked since there has not been any recent activity after it was closed. Please open a new issue for related bugs.

[![@lock](https://avatars0.githubusercontent.com/in/6672?s=60&v=4)](https://github.com/apps/lock) [lock](https://github.com/apps/lock) bot locked as **resolved** and limited conversation to collaborators [on 25 Apr](#)

 

[![@wenj91](https://avatars3.githubusercontent.com/u/12549338?s=80&v=4)](https://github.com/wenj91)

 

Write Preview

This conversation has been locked as **resolved** and limited to collaborators.

Assignees

No one assigned

Labels

None yet

Milestone

No milestone

Linked pull requests

Successfully merging a pull request may close this issue.

None yet

Notifications

Customize

### Notification settings

   

 Not subscribed

Only receive notifications from this issue when you have participated or have been @mentioned. Subscribed

Receive all notifications from this issue. Custom

You will only be notified for the events selected from the list below.  
If you participate or are @mentioned you will be subscribed.

 Closed

Receive a notification when this issue has been closed. Reopened

Receive a notification when this issue has been reopened.

Save Cancel

     Subscribe 

You’re not receiving notifications from this thread.

3 participants

 [![@harshavardhana](https://avatars1.githubusercontent.com/u/622699?s=52&v=4)](/harshavardhana) [ ![@riazarbi](https://avatars0.githubusercontent.com/u/13433911?s=52&v=4) ](/riazarbi) [![@eco-minio](https://avatars2.githubusercontent.com/u/41090896?s=52&v=4)](/eco-minio)