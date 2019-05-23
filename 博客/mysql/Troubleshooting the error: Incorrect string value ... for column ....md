# [Troubleshooting the error: Incorrect string value ... for column ...](http://www.cryer.co.uk/brian/mysql/trouble_incorrect_string_value.htm)

Symptoms
When performing an insert the following error is generated:

ERROR [HY000] [MySQL][ODBC 5.3(w) Driver][mysqld-5.6.15-log]Incorrect string value: '\\xF0\\x9F\\x94\\xA8 B...' for column 'Name' at row 1"

The column name (in my case 'Name' in the above) will be different, as may be the string value. If you are running the command in MySQL Workbench then you won't see see the "ERROR [HY000] [MySQL] [ODBC 5.3w Driver] [mysql-5.6.15-log] bit, as that is added by the ODBC driver.

Cause
In my case I was inserting data that was Unicode, into a database table that was using UTF-8.

Mostly doing this is fine, the problem comes with some Unicode characters. MySQL's implementation of UTF-8 means that it will use a maximum of three bytes for a character. However some Unicode characters requires 4 bytes of storage, at which point the MySQL UTF-8 implementation can't cope so the error is generated. Put another way, the MySQL implementation of UTF-8 is inadequate.

Remedy
The solution is to use utf8mb4 instead of utf8. Utf8mb4 will use up to 4 bytes of storage for each character and (unlike utf8) it can represent the full Unicode range.

That sounds simple, but there are a number of steps to this:

Check MySQL Version (5.5.3+)
Character set used on tables
Character set used internally by MySQL
Connection character set
Check MySQL Version
Support for utf8mb4 was first introduced in MySQL 5.5.3, so you will need to be running against MySQL 5.5.3 or later.

If you are unsure of which version of MySQL you are using, a simple query will show you the my MySql version and you can find notes on that here: Determine which version of MySQL you are connected to.

Character set used on tables
If you want to change the default character set and collation order for a database to utf8mb4 then use:

alter database db_name character set utf8mb4 collate utf8mb4_unicode_ci;

You don't need to change the default set and collation order, but it will help to avoid the issue with any new tables that you might create.

You will next need to change the character set and collation order for each table - at a minimum you will need to do it for the table that is causing the error.

alter table db_name.table_name convert to character set utf8mb4 collate utf8mb4_unicode_ci;

If you have a number of tables that you want updating then please refer to this article: "Change the charset and collation order for a MySQL database" as it provides a procedure for changing the character set and collation order for every table in a database.

Gotcha: There is one thing to be aware of when moving to utf8mb4 and that is index size. For index calculation purposes a MySQL utf8 field requires 3 bytes per character, or for utf8mb4 4 bytes per character. So utf8mb4 character strings take up more index space than utf8 ones, so if you have fields indexed that contain larger character strings then when converting to utf8mb4 it is possible that you might hit the error "Specified key was too long" from MySQL. If this is the case then you may have to shrink the size of the field or reconsider your indexes.

Character set used internally
The character set used internally by MySQL is probably utf-8 (so three byte). You can set the character set used by the server by adding the following line to the my.ini file:

[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

Connection character set
Finally you need to change the character set used for communication between the client and the server. For the ODBC driver this is specified by adding the following to the connection string:

charset=utf8mb4

Gotcha: When using the MySQL ODBC Connector ensure that you are using version 5.3 (Unicode) or later because earlier versions converted all data into UTF8 (yes, three bytes).

Other drivers should have a similar way of specifying the character set. For example:

set character set utf8mb4;

or

set names utf8mb4";

Alternatively you can change the defaults by editing the my.ini file and add the following lines to the [client] section:

[client]
default-character-set=utf8mb4

[mysqld]
character-set-client-handshake=false
character_set_server = utf8mb4
collation-server = utf8mb4_unicode_ci
init_connect='SET NAMES utf8mb4'

The character-set-client-handshake=false is in effect overriding anything that the client may request - either explicitly or implicitly. Personally I have opted not to include this, but I am setting the default-character-set.

If you are editing the my.ini file then you can make utf8mb4 the default by adding the following to the section indicated:

[mysql]
default-character-set = utf8mb4

If any of the above values replace an existing value then remove or comment out the old value, otherwise you may find that MySQL will refuse to start.

Remember that you will need to restart mysql after making changes to the my.ini file.

If it still does not work
If it still does not work then:

1. Check that one of the above setting's has not been missed. You can see the character sets used by each part of the process by issuing:

show variables like 'char%';

character_set_system will can be utf8, everything else should be utf8mb4. If it isn't then you have either missed one of the above steps or you are using a client that does not support utf8mb4.

2. If you are using the MySQL ODBC driver then I believe that it may still use utf-8 internally - this seems to be the case with the 5.3 ODBC Unicode driver. In which case the only work around will be to check for a newer driver, use a different connection method (so not ODBC) or screen out any four byte Unicode characters.

These notes have been tested against MySQL 5.6 and 5.5.

## PS
after modify the config, You must restart the service!