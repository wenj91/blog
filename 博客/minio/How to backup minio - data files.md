# [How to backup minio - data files](https://github.com/minio/minio/issues/4135)

[Jump to bottom](#)

How to backup minio - data files? #4135
=======================================

Closed

[akb2017](https://github.com/akb2017) opened this issue on 16 Apr 2017 · 17 comments

Closed

[How to backup minio - data files?](#) #4135
============================================

[akb2017](https://github.com/akb2017) opened this issue on 16 Apr 2017 · 17 comments

Assignees

 [![@harshavardhana](https://avatars0.githubusercontent.com/u/622699?s=40&v=4)](/harshavardhana) 

Labels

[community](https://github.com/minio/minio/labels/community "community") [not our bug](https://github.com/minio/minio/labels/not%20our%20bug "not our bug") [priority: low](https://github.com/minio/minio/labels/priority%3A%20low "priority: low")

Milestone

[Next Release](/minio/minio/milestone/15 "Next Release")

Comments
--------

[![@akb2017](https://avatars0.githubusercontent.com/u/26516468?s=88&v=4)](https://github.com/akb2017)

Copy link Quote reply

### [![@akb2017](https://avatars1.githubusercontent.com/u/26516468?s=60&v=4)](https://github.com/akb2017) ** [akb2017](https://github.com/akb2017) ** commented [on 16 Apr 2017](#) 

My question is how to backup minio data?  
In the worst scenario if i lost all the data in all the servers how do i recover it?  
Should i take backup manually or is there any other solution for that ?

[![@harshavardhana](https://avatars0.githubusercontent.com/u/622699?s=88&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

There some tools which we recommend such as - Restic https://docs.minio.io/docs/restic-with-minio - Rclone https://docs.minio.io/docs/rclone-with-minio-server - Cloudberry Backup “Setup an automated data backup system with Minio and CloudBerry” @tiwari\_nitish https://blog.minio.io/setup-an-automated-data-backup-system-with-minio-and-cloudberry-cc8dc7f178b9 and https://www.cloudberrylab.com/solutions/minio \_Originally posted by @harshavardhana in https://github.com/minio/minio/issues/4135#issuecomment-294360874\_

Create issue

Member

### [![@harshavardhana](https://avatars3.githubusercontent.com/u/622699?s=60&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana) ** [harshavardhana](https://github.com/harshavardhana) ** commented [on 17 Apr 2017](#) 

There some tools which we recommend such as

*   Restic [https://docs.minio.io/docs/restic-with-minio](https://docs.minio.io/docs/restic-with-minio)
*   Rclone [https://docs.minio.io/docs/rclone-with-minio-server](https://docs.minio.io/docs/rclone-with-minio-server)
*   Cloudberry Backup “Setup an automated data backup system with Minio and CloudBerry” @tiwari\_nitish [https://blog.minio.io/setup-an-automated-data-backup-system-with-minio-and-cloudberry-cc8dc7f178b9](https://blog.minio.io/setup-an-automated-data-backup-system-with-minio-and-cloudberry-cc8dc7f178b9) and [https://www.cloudberrylab.com/solutions/minio](https://www.cloudberrylab.com/solutions/minio)

 

👍 1

[![@harshavardhana](https://avatars3.githubusercontent.com/u/622699?s=60&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana) [harshavardhana](https://github.com/harshavardhana) added the  [community](https://github.com/minio/minio/labels/community)  label [on 17 Apr 2017](#)

[![@harshavardhana](https://avatars3.githubusercontent.com/u/622699?s=60&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana) [harshavardhana](https://github.com/harshavardhana) self-assigned this [on 17 Apr 2017](#)

[![@akb2017](https://avatars0.githubusercontent.com/u/26516468?s=88&v=4)](https://github.com/akb2017)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

What about distributed minio how can i take backup ?In the same way or is there any different approach? \_Originally posted by @akb2017 in https://github.com/minio/minio/issues/4135#issuecomment-294361837\_

Create issue

Author

### [![@akb2017](https://avatars1.githubusercontent.com/u/26516468?s=60&v=4)](https://github.com/akb2017) ** [akb2017](https://github.com/akb2017) ** commented [on 17 Apr 2017](#) 

What about distributed minio how can i take backup ?In the same way or is there any different approach?

[![@harshavardhana](https://avatars0.githubusercontent.com/u/622699?s=88&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

Distributed Minio is no different those articles apply to distributed Minio as well. \_Originally posted by @harshavardhana in https://github.com/minio/minio/issues/4135#issuecomment-294362238\_

Create issue

Member

### [![@harshavardhana](https://avatars3.githubusercontent.com/u/622699?s=60&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana) ** [harshavardhana](https://github.com/harshavardhana) ** commented [on 17 Apr 2017](#) 

Distributed Minio is no different those articles apply to distributed Minio as well.

[![@akb2017](https://avatars0.githubusercontent.com/u/26516468?s=88&v=4)](https://github.com/akb2017)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

but for backup purpose it needs to create one more minio server right? \_Originally posted by @akb2017 in https://github.com/minio/minio/issues/4135#issuecomment-294362718\_

Create issue

Author

### [![@akb2017](https://avatars1.githubusercontent.com/u/26516468?s=60&v=4)](https://github.com/akb2017) ** [akb2017](https://github.com/akb2017) ** commented [on 17 Apr 2017](#) 

but for backup purpose it needs to create one more minio server right?

[![@deekoder](https://avatars0.githubusercontent.com/u/1700901?s=60&u=c36a17d28422d58eff56e134555dd6dd17100a1a&v=4)](https://github.com/deekoder) [deekoder](https://github.com/deekoder) added this to the Edge cache milestone [on 17 Apr 2017](#)

[![@deekoder](https://avatars0.githubusercontent.com/u/1700901?s=60&u=c36a17d28422d58eff56e134555dd6dd17100a1a&v=4)](https://github.com/deekoder) [deekoder](https://github.com/deekoder) added the  [priority: low](https://github.com/minio/minio/labels/priority%3A%20low)  label [on 17 Apr 2017](#)

[![@harshavardhana](https://avatars0.githubusercontent.com/u/622699?s=88&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

\> but for backup purpose it needs to create one more minio server right? It is upto you it could be another NAS device, Minio Server or even Amazon S3. \_Originally posted by @harshavardhana in https://github.com/minio/minio/issues/4135#issuecomment-294367060\_

Create issue

Member

### [![@harshavardhana](https://avatars3.githubusercontent.com/u/622699?s=60&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana) ** [harshavardhana](https://github.com/harshavardhana) ** commented [on 17 Apr 2017](#)  •  

edited

> but for backup purpose it needs to create one more minio server right?

It is upto you it could be another NAS device, Minio Server or even Amazon S3.

[![@krishnasrinivas](https://avatars2.githubusercontent.com/u/634494?s=88&u=8a6f5af964fe7f3c1467566d6c5c5bf631dbfaab&v=4)](https://github.com/krishnasrinivas)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

@akb2017 you can use \`mc mirror\` as well and do continuous backup: https://docs.minio.io/docs/minio-client-complete-guide#mirror \`\`\` mc mirror -w src dst \`\`\` \_Originally posted by @krishnasrinivas in https://github.com/minio/minio/issues/4135#issuecomment-294367826\_

Create issue

Member

### [![@krishnasrinivas](https://avatars1.githubusercontent.com/u/634494?s=60&u=8a6f5af964fe7f3c1467566d6c5c5bf631dbfaab&v=4)](https://github.com/krishnasrinivas) ** [krishnasrinivas](https://github.com/krishnasrinivas) ** commented [on 17 Apr 2017](#) 

[@akb2017](https://github.com/akb2017) you can use `mc mirror` as well and do continuous backup:

[](https://docs.minio.io/docs/minio-client-complete-guide>https://docs.minio.io/docs/minio-client-complete-guide#mirror</a></p>
<pre><code>mc mirror -w src dst
</code></pre>
      </td>
    </tr>
  </tbody>
</table>
</task-lists>


          
<div class=)

[](https://docs.minio.io/docs/minio-client-complete-guide>https://docs.minio.io/docs/minio-client-complete-guide#mirror</a></p>
<pre><code>mc mirror -w src dst
</code></pre>
      </td>
    </tr>
  </tbody>
</table>
</task-lists>


          
<div class=)

[](https://docs.minio.io/docs/minio-client-complete-guide>https://docs.minio.io/docs/minio-client-complete-guide#mirror</a></p>
<pre><code>mc mirror -w src dst
</code></pre>
      </td>
    </tr>
  </tbody>
</table>
</task-lists>


          
<div class=)[![@akb2017](https://avatars0.githubusercontent.com/u/26516468?s=88&v=4)](https://github.com/akb2017)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

But in order to backup the distributed minio it requires to backup the data from all the servers right? Is there any easy way to do it? \_Originally posted by @akb2017 in https://github.com/minio/minio/issues/4135#issuecomment-294368375\_

Create issue

Author

### [![@akb2017](https://avatars1.githubusercontent.com/u/26516468?s=60&v=4)](https://github.com/akb2017) ** [akb2017](https://github.com/akb2017) ** commented [on 17 Apr 2017](#) 

But in order to backup the distributed minio it requires to backup the data from all the servers right? Is there any easy way to do it?

[![@krishnasrinivas](https://avatars2.githubusercontent.com/u/634494?s=88&u=8a6f5af964fe7f3c1467566d6c5c5bf631dbfaab&v=4)](https://github.com/krishnasrinivas)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

\`mc\` works on top of S3, so you should not try to backup from the backend. \`mc\` uses S3 protocol for upload/download from minio server. \_Originally posted by @krishnasrinivas in https://github.com/minio/minio/issues/4135#issuecomment-294368586\_

Create issue

Member

### [![@krishnasrinivas](https://avatars1.githubusercontent.com/u/634494?s=60&u=8a6f5af964fe7f3c1467566d6c5c5bf631dbfaab&v=4)](https://github.com/krishnasrinivas) ** [krishnasrinivas](https://github.com/krishnasrinivas) ** commented [on 17 Apr 2017](#) 

`mc` works on top of S3, so you should not try to backup from the backend. `mc` uses S3 protocol for upload/download from minio server.

[![@akb2017](https://avatars0.githubusercontent.com/u/26516468?s=88&v=4)](https://github.com/akb2017)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

OK thank you \_Originally posted by @akb2017 in https://github.com/minio/minio/issues/4135#issuecomment-294368652\_

Create issue

Author

### [![@akb2017](https://avatars1.githubusercontent.com/u/26516468?s=60&v=4)](https://github.com/akb2017) ** [akb2017](https://github.com/akb2017) ** commented [on 17 Apr 2017](#) 

OK thank you

[![@akb2017](https://avatars1.githubusercontent.com/u/26516468?s=60&v=4)](https://github.com/akb2017) [akb2017](https://github.com/akb2017) closed this [on 17 Apr 2017](#)

[![@harshavardhana](https://avatars3.githubusercontent.com/u/622699?s=60&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana) [harshavardhana](https://github.com/harshavardhana) added the  [wont fix](https://github.com/minio/minio/labels/wont%20fix)  label [on 17 Apr 2017](#)

[![@seafoodbuffet](https://avatars1.githubusercontent.com/u/1646003?s=60&v=4)](https://github.com/seafoodbuffet) [seafoodbuffet](https://github.com/seafoodbuffet) mentioned this issue [on 23 May 2017](#ref-issue-230572852) 

[Taking Backups of MinIO #4398](/minio/minio/issues/4398)

Closed

[![@zqkou](https://avatars1.githubusercontent.com/u/20944084?s=88&v=4)](https://github.com/zqkou)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

@harshavardhana One of your recommendation is Restic, which I took a look is great backup/restore tool. But I only see articles talk about how to backup data TO minio. I didn't see how to backup data FROM minio. Could you please share some thoughts on this? \_Originally posted by @zqkou in https://github.com/minio/minio/issues/4135#issuecomment-526096773\_

Create issue

### [![@zqkou](https://avatars0.githubusercontent.com/u/20944084?s=60&v=4)](https://github.com/zqkou) ** [zqkou](https://github.com/zqkou) ** commented [on 29 Aug 2019](#) 

[@harshavardhana](https://github.com/harshavardhana)  
One of your recommendation is Restic, which I took a look is great backup/restore tool. But I only see articles talk about how to backup data TO minio. I didn't see how to backup data FROM minio. Could you please share some thoughts on this?

 

👍 8

[![@GabrielDumbrava](https://avatars1.githubusercontent.com/u/2076656?s=88&v=4)](https://github.com/GabrielDumbrava)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

\[Handybackup\](https://www.handybackup.net/minio-backup.shtml) can also backup from minio, but also \[rclone\](https://docs.min.io/docs/rclone-with-minio-server.html) might do the trick. You may still want to run minio in \[distributed mode\](https://docs.min.io/docs/distributed-minio-quickstart-guide.html) so that your data is being written in more than one node at the same time. \_Originally posted by @GabrielDumbrava in https://github.com/minio/minio/issues/4135#issuecomment-538390306\_

Create issue

### [![@GabrielDumbrava](https://avatars0.githubusercontent.com/u/2076656?s=60&v=4)](https://github.com/GabrielDumbrava) ** [GabrielDumbrava](https://github.com/GabrielDumbrava) ** commented [on 4 Oct 2019](#) 

[Handybackup](https://www.handybackup.net/minio-backup.shtml) can also backup from minio, but also [rclone](https://docs.min.io/docs/rclone-with-minio-server.html) might do the trick.

You may still want to run minio in [distributed mode](https://docs.min.io/docs/distributed-minio-quickstart-guide.html) so that your data is being written in more than one node at the same time.

[![@trankchung](https://avatars2.githubusercontent.com/u/573808?s=88&u=a10ca235739ec29d16071e72d0727e897a6084e4&v=4)](https://github.com/trankchung)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

I’m a little confused on all these tools if someone can please explain. I have a minio cluster that I want to backup its volumes into an NFS. All these tools documentation is about syncing a directory to a bucket instead of a bucket out into a directory. Am I missing something? Thanks. \_Originally posted by @trankchung in https://github.com/minio/minio/issues/4135#issuecomment-541328097\_

Create issue

### [![@trankchung](https://avatars1.githubusercontent.com/u/573808?s=60&u=a10ca235739ec29d16071e72d0727e897a6084e4&v=4)](https://github.com/trankchung) ** [trankchung](https://github.com/trankchung) ** commented [on 12 Oct 2019](#) 

I’m a little confused on all these tools if someone can please explain. I have a minio cluster that I want to backup its volumes into an NFS. All these tools documentation is about syncing a directory to a bucket instead of a bucket out into a directory. Am I missing something? Thanks.

 

👍 2

[![@ZVilusinsky](https://avatars3.githubusercontent.com/u/47591967?s=88&u=a09462a9869353f360868edbdafdf478eec8f0de&v=4)](https://github.com/ZVilusinsky)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

\> I’m a little confused on all these tools if someone can please explain. I have a minio cluster that I want to backup its volumes into an NFS. All these tools documentation is about syncing a directory to a bucket instead of a bucket out into a directory. Am I missing something? Thanks. Just backup the drive/folder where you store minio data. At least that is our current solution as I did not find any info on dedicated minio backup and replacing the current version with a backup works. \_Originally posted by @ZVilusinsky in https://github.com/minio/minio/issues/4135#issuecomment-555091826\_

Create issue

### [![@ZVilusinsky](https://avatars0.githubusercontent.com/u/47591967?s=60&u=a09462a9869353f360868edbdafdf478eec8f0de&v=4)](https://github.com/ZVilusinsky) ** [ZVilusinsky](https://github.com/ZVilusinsky) ** commented [on 19 Nov 2019](#) 

> I’m a little confused on all these tools if someone can please explain. I have a minio cluster that I want to backup its volumes into an NFS. All these tools documentation is about syncing a directory to a bucket instead of a bucket out into a directory. Am I missing something? Thanks.

Just backup the drive/folder where you store minio data. At least that is our current solution as I did not find any info on dedicated minio backup and replacing the current version with a backup works.

[![@l0nax](https://avatars2.githubusercontent.com/u/29659953?s=88&u=01b40c160e224232fe42bf4c4c3c2051bfd2b82c&v=4)](https://github.com/l0nax)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

\> Just backup the drive/folder where you store minio data. At least that is our current solution as I did not find any info on dedicated minio backup and replacing the current version with a backup works. But does MinIO \_lock\_ the repository to prevent data loss. I mean what is if currently a backup is created and at the same time some data changes on our MinIO-Server? Does this destroy the data? @harshavardhana @krishnasrinivas \_Originally posted by @l0nax in https://github.com/minio/minio/issues/4135#issuecomment-569218412\_

Create issue

### [![@l0nax](https://avatars1.githubusercontent.com/u/29659953?s=60&u=01b40c160e224232fe42bf4c4c3c2051bfd2b82c&v=4)](https://github.com/l0nax) ** [l0nax](https://github.com/l0nax) ** commented [on 27 Dec 2019](#)  •  

edited

> Just backup the drive/folder where you store minio data. At least that is our current solution as I did not find any info on dedicated minio backup and replacing the current version with a backup works.

But does MinIO _lock_ the repository to prevent data loss.  
I mean what is if currently a backup is created and at the same time some data changes on our MinIO-Server?  
Does this destroy the data? [@harshavardhana](https://github.com/harshavardhana) [@krishnasrinivas](https://github.com/krishnasrinivas)

[![@krishnasrinivas](https://avatars2.githubusercontent.com/u/634494?s=88&u=8a6f5af964fe7f3c1467566d6c5c5bf631dbfaab&v=4)](https://github.com/krishnasrinivas)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

@l0nax \`mc mirror src dst\` computes a diff of files between \`src\` and \`dst\` and starts copying files one by one. If you try to overwrite a file in \`src\` that is being copied, the overwrite gets blocked till the file is copied as we hold a lock. \_Originally posted by @krishnasrinivas in https://github.com/minio/minio/issues/4135#issuecomment-569457131\_

Create issue

Member

### [![@krishnasrinivas](https://avatars1.githubusercontent.com/u/634494?s=60&u=8a6f5af964fe7f3c1467566d6c5c5bf631dbfaab&v=4)](https://github.com/krishnasrinivas) ** [krishnasrinivas](https://github.com/krishnasrinivas) ** commented [on 29 Dec 2019](#) 

[@l0nax](https://github.com/l0nax) `mc mirror src dst` computes a diff of files between `src` and `dst` and starts copying files one by one. If you try to overwrite a file in `src` that is being copied, the overwrite gets blocked till the file is copied as we hold a lock.

[![@zadigus](https://avatars1.githubusercontent.com/u/8761254?s=60&u=7f2b1c866ff7447694fcf2c0a9f0c104cbf27988&v=4)](https://github.com/zadigus) [zadigus](https://github.com/zadigus) mentioned this issue [on 25 Jan](#ref-issue-555094245) 

[Organize backups for the assets service shopozor/services#162](/shopozor/services/issues/162)

Open

[![@phucduong86](https://avatars0.githubusercontent.com/u/21692768?s=88&u=c1b31ef0e71e9e7d0976ab9549c4fbc1cbdf4f98&v=4)](https://github.com/phucduong86)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

\> I have a minio cluster that I want to backup its volumes into an NFS. All these tools documentation is about syncing a directory to a bucket instead of a bucket out into a directory. Am I missing something? Thanks. I agree that they are lacking in example. When you reverse the order of your arguments it would copy the files from the bucket to local. Here's an example of the command I use: \`\`\` mc mirror --remove --preserve $MINIO\_ENV/<bucket> $BACKUPS\_DIR/$BACKUP\_NAME \`\`\` Where: - \*\*$MINIO\_ENV\*\* is your Min.IO remote host (eg. 'myminio') - \*\*$BACKUPS\_DIR/$BACKUP\_NAME\*\* is the absolute path to your local folder \_Originally posted by @phucduong86 in https://github.com/minio/minio/issues/4135#issuecomment-642718894\_

Create issue

### [![@phucduong86](https://avatars3.githubusercontent.com/u/21692768?s=60&u=c1b31ef0e71e9e7d0976ab9549c4fbc1cbdf4f98&v=4)](https://github.com/phucduong86) ** [phucduong86](https://github.com/phucduong86) ** commented [on 11 Jun](#)  •  

edited

> I have a minio cluster that I want to backup its volumes into an NFS. All these tools documentation is about syncing a directory to a bucket instead of a bucket out into a directory. Am I missing something? Thanks.

I agree that they are lacking in example.  
When you reverse the order of your arguments it would copy the files from the bucket to local.  
Here's an example of the command I use:

    mc mirror --remove --preserve $MINIO_ENV/<bucket> $BACKUPS_DIR/$BACKUP_NAME
    

Where:

*   **$MINIO\_ENV** is your Min.IO remote host (eg. 'myminio')
*   **$BACKUPS\_DIR/$BACKUP\_NAME** is the absolute path to your local folder

 

👍 1

[![@harshavardhana](https://avatars3.githubusercontent.com/u/622699?s=60&u=2f7f11171602b494cb27b5642ea9af3261ec927d&v=4)](https://github.com/harshavardhana) [harshavardhana](https://github.com/harshavardhana) added  [not our bug](https://github.com/minio/minio/labels/not%20our%20bug)  and removed  [wont fix](https://github.com/minio/minio/labels/wont%20fix)  labels [on 11 Jun](#)

[![@satyamuralidhar](https://avatars2.githubusercontent.com/u/38804803?s=88&u=0c0b25699615028f1dde0c21c87cc1ba4571f54b&v=4)](https://github.com/satyamuralidhar)

Copy link Quote reply Reference in new issue

### Reference in new issue

Repository

 minio Repositories 

Title

Body

ERROR: 2020/07/10 16:15:05.924450 Finish LSN of backup base\_000000010000000000000005 greater than current LSN \_Originally posted by @satyamuralidhar in https://github.com/minio/minio/issues/4135#issuecomment-656778141\_

Create issue

### [![@satyamuralidhar](https://avatars1.githubusercontent.com/u/38804803?s=60&u=0c0b25699615028f1dde0c21c87cc1ba4571f54b&v=4)](https://github.com/satyamuralidhar) ** [satyamuralidhar](https://github.com/satyamuralidhar) ** commented [24 days ago](#) 

ERROR: 2020/07/10 16:15:05.924450 Finish LSN of backup base\_000000010000000000000005 greater than current LSN

[![@minio](https://avatars0.githubusercontent.com/u/695951?s=60&v=4)](https://github.com/minio) [minio](https://github.com/minio) locked as **resolved** and limited conversation to collaborators [23 days ago](#)

 

[![@wenj91](https://avatars3.githubusercontent.com/u/12549338?s=80&v=4)](https://github.com/wenj91)

 

Write Preview

This conversation has been locked as **resolved** and limited to collaborators.

Assignees

 [![@harshavardhana](https://avatars0.githubusercontent.com/u/622699?s=40&v=4)](/harshavardhana) [harshavardhana](/harshavardhana) 

Labels

[community](https://github.com/minio/minio/labels/community "community") [not our bug](https://github.com/minio/minio/labels/not%20our%20bug "not our bug") [priority: low](https://github.com/minio/minio/labels/priority%3A%20low "priority: low")

Milestone

[**Next Release**](/minio/minio/milestone/15 "Next Release")

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

11 participants

 [![@trankchung](https://avatars1.githubusercontent.com/u/573808?s=52&v=4)](/trankchung) [ ![@harshavardhana](https://avatars1.githubusercontent.com/u/622699?s=52&v=4) ](/harshavardhana) [ ![@krishnasrinivas](https://avatars1.githubusercontent.com/u/634494?s=52&v=4) ](/krishnasrinivas) [ ![@deekoder](https://avatars1.githubusercontent.com/u/1700901?s=52&v=4) ](/deekoder) [ ![@GabrielDumbrava](https://avatars2.githubusercontent.com/u/2076656?s=52&v=4) ](/GabrielDumbrava) [ ![@zqkou](https://avatars2.githubusercontent.com/u/20944084?s=52&v=4) ](/zqkou) [ ![@phucduong86](https://avatars3.githubusercontent.com/u/21692768?s=52&v=4) ](/phucduong86) [ ![@akb2017](https://avatars3.githubusercontent.com/u/26516468?s=52&v=4) ](/akb2017) [ ![@l0nax](https://avatars2.githubusercontent.com/u/29659953?s=52&v=4) ](/l0nax) [ ![@satyamuralidhar](https://avatars3.githubusercontent.com/u/38804803?s=52&v=4) ](/satyamuralidhar) [![@ZVilusinsky](https://avatars3.githubusercontent.com/u/47591967?s=52&v=4)](/ZVilusinsky)