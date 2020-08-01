# [Change limit for “Mysql Row size too large”](https://stackoverflow.com/questions/15585602/change-limit-for-mysql-row-size-too-large)

Row size too large (> 8126). Changing some columns to TEXT or BLOB or using ROW_FORMAT=DYNAMIC or ROW_FORMAT=COMPRESSED may help. In current row format, BLOB prefix of 768 bytes is stored inline.

出现这个问题mariadb版本：10.1.x
试过这个问题里面的各种办法，还有其他办法，都解决不了问题

最后通过安装最新的mariadb：10.3.x 重新迁移数据，问题没了