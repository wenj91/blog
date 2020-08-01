# [restore table from .frm and .ibd file?](https://dba.stackexchange.com/questions/16875/restore-table-from-frm-and-ibd-file)

16

I have recovered my MySQL 5.5 *.ibd and *.frm files with using MySQL Utilites and MariaDB 10.

## 1) Generating Create SQLs.
You can get your create sql's from frm file. You must use : https://downloads.mysql.com/archives/utilities/

shell> mysqlfrm --server=root:pass@localhost:3306 c:\MY\t1.frm --port=3310

Other way you may have your create sql's.

## 2) Create Your Tables
Create your tables on the database.

## 3) alter table xxx discard tablespace
Discard your tables which do you want to replace your *.ibd files.

## 4) Copy your *.ibd files (MySQL Or MariaDB) to MariaDB's data path
First i try to use MySQL 5.5 and 5.6 to restrore, but database crashes and immediately stops about tablespace id broken error. (ERROR 1030 (HY000): Got error -1 from storage engine)
After i have used MariaDB 10.1.8, and i have succesfully recovered my data.

## 5) alter table xxx import tablespace
When you run this statement, MariaDB warns about file but its not important than to recover your data :) Database still continues and you can see your data.

I hope this information will be helpful for you.