# [Disable ONLY_FULL_GROUP_BY](https://stackoverflow.com/questions/23921117/disable-only-full-group-by)

To keep your current mysql settings and disable ONLY_FULL_GROUP_BY I suggest to visit your phpmyadmin or whatever client you are using and type:

SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY','') copy_me

next copy result to your my.ini file.

mint: sudo nano /etc/mysql/my.cnf

ubuntu 16 and up: sudo nano /etc/mysql/my.cnf

ubuntu 14-16: /etc/mysql/mysql.conf.d/mysqld.cnf

Caution! copy_me result can contain a long text which might be trimmed by default. Make sure you copy whole text!
old answer:

If you want to disable permanently error "Expression #N of SELECT list is not in GROUP BY clause and contains nonaggregated column 'db.table.COL' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by" do those steps:

sudo nano /etc/mysql/my.cnf
Add this to the end of the file

[mysqld]  
sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
sudo service mysql restart to restart MySQL

This will disable ONLY_FULL_GROUP_BY for ALL users