[Connecting to MySQL through Docker](https://medium.com/coderscorner/connecting-to-mysql-through-docker-997aa2c090cc)

# Connecting to MySQL through Docker
Accessing MySQL using a web interface through docker

Step 1 : Pull MySql image from docker hub. Following command will pull the latest mysql image.

docker pull mysql
Step 2: Run a container from this image. ‘-name’ gives a name to the container. ‘ -e’ specifies run time variables you need to set. Set the password for the MySQL root user using ‘MYSQL_ROOT_PASSWORD’. ‘-d’ tells the docker to run the container in background.

docker run --name=testsql -e MYSQL_ROOT_PASSWORD=rukshani -d mysql 
This will output a container id; which means that the container is running in the background properly.

Step 3: Then check the status of the container by issuing, ‘docker ps’ command

docker ps

Output
Now you should be able to see that MySQL is running on port 3306.

Step 4: To checkout the logs of the running container use the following command

docker logs testsql
Step 5: Find the IP of the container using following. Check out the “IPAddress” from the output, this will tell you the ip address.

docker inspect testsql
Now you should be able to connect to MySQL using this ip address on port 3306.

Accessing MySQL through a Web Interface
phpMyAdmin gives us a web interface to access MySQL database and we are going to use that to access and administer MySQL database that we have set up earlier.

Step 1: Pull phpMyAdmin image from docker hub

docker pull phpmyadmin/phpmyadmin
Step 2: To link our existing MySQL container with phpMyAdmin application use the following.

docker run --name myadmin -d --link testsql:db -p 8080:80 phpmyadmin/phpmyadmin 
That’s it! Now open up a browser and go to ‘http://localhost:8080/’.

Use the username as ‘root’ and give the password that you set earlier for ‘‘MYSQL_ROOT_PASSWORD’ and you should be able to login and manage your mysql database through phpMyAdmin.