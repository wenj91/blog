# [Configuring a Datasource with MySQL on WildFly](http://www.mastertheboss.com/jboss-server/jboss-datasource/configuring-a-datasource-with-mysql-on-wildfly)

Configuring a Datasource with MySQL on WildFly
In this tutorial we will learn how to install and configure a Datasource on WildFly which uses MySQL or MariaDB as Database.

In order to install MySQL, please follow the Installation Guide available: https://dev.mysql.com/doc/mysql-getting-started/en/#mysql-getting-started-installing

As an alternative, if you have available docker, you can start a MySQL instance in a minute with:

1
$ docker run -d --name mysql -e MYSQL_USER=jboss -e MYSQL_PASSWORD=jboss -e MYSQL_DATABASE=mysqlschema -e MYSQL_ROOT_PASSWORD=secret mysql
Now check the IP Address which has been assigned to your Container as follows:

1
2
$ sudo docker inspect --format '{{ .NetworkSettings.IPAddress }}' mysql
172.17.0.2
Next you will need a JDBC Driver for MySQL. MySQL Connector/J is the official JDBC driver for MySQL and it's available at: https://dev.mysql.com/downloads/connector/j/

You can download the platform independent zip driver and place in a folder of your likes, for example in the /var folder.

Hack: you can also download directly the JDBC Driver from Maven with a single command:

1
wget -q "http://search.maven.org/remotecontent?filepath=mysql/mysql-connector-java/5.1.32/mysql-connector-java-5.1.32.jar" -O mysql-connector-java.jar
Installing the MySQL Datasource on WildFly in Standalone mode
Now start the Command Line Interface:

1
$ ./jboss-cli.sh -c
We will The following command will install the com.mysql module creating for you the module directory structure:

1
module add --name=com.mysql --resources=/var/mysql-connector-java-5.1.31-bin.jar --dependencies=javax.api,javax.transaction.api
Next, we need to install the JDBC driver using the above defined module:

1
/subsystem=datasources/jdbc-driver=mysql:add(driver-name=mysql,driver-module-name=com.mysql)
Finally, install the data source by using the data-source shortcut command, which requires as input the Pool name, the JNDI bindings, the JDBC Connection parameters and finally the security settings. Assumed that the MySQL Database is available at the IP Address 172.17.0.2:

1
data-source add --jndi-name=java:/MySqlDS --name=MySQLPool --connection -url=jdbc:mysql://172.17.0.2:3306/mysqlschema --driver-name=mysql --user-name=jboss --password=jboss
You can check that the datasource has been installed correctly by issuing the following command:

1
/subsystem=datasources/data-source=MySQLPool:test-connection-in-pool
Installing the MySQL Datasource on WildFly in Domain mode
Installing the MySQL Datasource in Domain mode requires that you associate the Datasource with the Domain Profile. The first step (the module installation) is required to be executed on every Host Controller:

1
module add --name=com.mysql --resources=/var/mysql-connector-java-5.1.31-bin.jar --dependencies=javax.api,javax.transaction.api
Next, we need to install the JDBC driver on a server Profile:

1
/profile=full-ha/subsystem=datasources/jdbc-driver=mysql:add(driver-name=mysql,driver-module-name=com.mysql)
Finally, install the data source by using the data-source shortcut command, which requires also the --profile additional option:

1
data-source add --jndi-name=java:/MySqlDS --name=MySQLPool --connection -url=jdbc:mysql://172.17.0.2:3306/mysqldb --driver-name=mysql --user-name=jboss --password=jboss --profile=full-ha
Creating an XA Datasource
If you are going to use an XA Datasource in your applications there are some changes that you need to apply to your CLI scripts. Start as usual by creating the module at first:

1
module add --name=com.mysql --resources=/var/mysql-connector-java-5.1.31-bin.jar --dependencies=javax.api,javax.transaction.api
Next, install the JDBC driver using the above module:

1
/subsystem=datasources/jdbc-driver=mysql:add(driver-name=mysql,driver-module-name=com.mysql)
The twist now is to use the xa-data-source shortcut command in order to create the XA Datasource. This command requires that you specify the Datasource name, its JNDI Bindings, the XA Datasource

class, the Security settings and, finally, at least one property must be specified (in our case we have specified the Server host name):

1
xa-data-source add --name=MySqlDSXA --jndi-name=java:/MySqlDSXA --driver-name=mysql --xa-datasource-class=com.mysql.jdbc.jdbc2.optional.MysqlXADataSource --user -name=jboss --password=jboss --xa-datasource-properties=[{ServerName=172.17.0.2}]
Next, you can add additional properties needed for your Database connections, such as the Database schema:

1
/subsystem=datasources/xa-data-source=MySqlDSXA/xa-datasource-properties=DatabaseName:add(value="mysqlschema")
Tuning MySQL
Besides the standard datasource tuning information (you can read also here) MySQL has some specific parameters which can be used for tuning:

 

innodb_buffer_pool_size: this is the most important setting to look at after you have completed the Database installation. The buffer pool is where data and indexes are cached: having it as large as possible will ensure you use memory and not disks for most read operations. Typical values are 5-6GB (8GB RAM), 20-25GB (32GB RAM), 100-120GB (128GB RAM).
innodb_log_file_size: this is the size of the Database redo logs. The redo logs are used to make sure writes are fast and durable and also during crash recovery. Since MySQL 5.5 so you can now have good write performance and fast crash recovery. Starting with innodb_log_file_size = 512M (giving 1GB of redo logs) should give you plenty of room for writes. If you know your application is write-intensive and you are using MySQL 5.6, you can start with innodb_log_file_size = 4G.
max_connections: Even if you configure WildFly with a huge Connection Pool, if this parameter is not adequate you might end up with the ‘Too many connections’ error because max_connections is too low. The main drawback of high values for max_connections (like 1000 or more) is that the server will become unresponsive if for any reason it has to run 1000 or more active transactions. Using a connection pool at the application level or a thread pool at the MySQL level can help here.
For example, as you can see in the following picture, a max-pool-size configuration of 500 will not be enough to enable that number of connections if MySQL is configured with default settings:

mysql wildfly jboss datasource configuration

If you are using Docker, you can easily configure the following environment variables to set the above parameters:

1
2
3
4
5
MYSQL_INNODB_BUFFER_POOL_SIZE (default: 32M or 50% of available memory): The size of the buffer pool where InnoDB caches table and index data
 
MYSQL_INNODB_LOG_FILE_SIZE (default: 8M or 15% of available available): The size of each log file in a log group
 
MYSQL_MAX_CONNECTIONS (default: 151): The maximum permitted number of simultaneous client connections
For example:

1
$ docker run -d --name mysql -e MYSQL_USER=jboss -e MYSQL_PASSWORD=jboss -e MYSQL_DATABASE=mysqlschema -e MYSQL_ROOT_PASSWORD=secret mysql -e MYSQL_MAX_CONNECTIONS=500