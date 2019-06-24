# [How to run multiple services in a Docker container](https://medium.com/@karthi.net/how-to-run-multiple-services-in-a-docker-container-5919fcc981a6)

Docker as we know,is an open platform for developers and sysadmins to build, ship, and run distributed applications, whether on laptops, data center VMs, or the cloud.

Before we move on to actual article,some key points about containers :

Containers are an abstraction at the app layer that packages code and dependencies together. Multiple containers can run on the same machine and share the OS kernel with other containers, each running as isolated processes in user space. Containers take up less space than VMs (container images are typically tens of MBs in size), and start almost instantly.
A container image is a lightweight, stand-alone, executable package of a piece of software that includes everything needed to run it: code, runtime, system tools, system libraries, settings.
Containers run apps natively on the host machine’s kernel. They have better performance characteristics than virtual machines that only get virtual access to host resources through a hypervisor. Containers can get native access, each one running in a discrete process, taking no more memory than any other executable.
By default,main running process for Docker is the ENTRYPOINT and/or CMD at the end of the Dockerfile.Per Docker guidelines, it is recommended that you separate areas of concern by using one service per container but there could be scenarios where you would have to group services and run it in single container.

In this post, we are going to take look at how to run multiple services in Docker using Supervisord process manager.Supervisor is a client/server system that allows users to monitor and control a number of processes on UNIX-like operating systems.

Step #1. Verify Docker Installation
Its assumed that you already have Docker installation,check the installation by running following command docker run hello-world:

Image — Validate Docker Installation
If you need assistance on Docker installation,check out here.
Run docker — version to check the version of the docker you’re running.
Check Docker version
Image — Check Docker version
OK, now we have got the docker setup,next step is to define the docker container.
Step #2. Create Dockerfile for our container
Before we move on to Dockerfile,lets check out some overview on supervisord. Supervisor is responsible for starting child programs at its own invocation, responding to commands from clients, restarting crashed or exited subprocesseses , logging its subprocess stdout and stderr output, and generating and handling “events” corresponding to points in subprocess lifetimes. Supervisor process typically uses a configuration file. This is typically located in /etc/supervisord.conf.

For us to use Supervisord process manager in Docker,child programs for example Angular UI and Spring Boot Services needs to be configured in supervisord.conf file so that supervisord can spawn them during Docker initialization process.

To begin with create a new folder and then create a file in it named “Dockerfile” with the following content.
# Dockerfile
FROM rickw/ubuntu12-java8 
MAINTAINER  Author Name author@email.com
Here I have Ubuntu 12 Image with Java 8 pre-installed as baseimage.Once we have the baseimage set,next step is to run pre-install steps for Supervisor process manager installation.
#cleanup
RUN add-apt-repository -r ppa:webupd8team/java
RUN apt-get update && apt-get install -y apache2 && apt-get clean && rm -rf /var/lib/apt/lists/*
RUN \
  sed -i 's/# \(.*multiverse$\)/\1/g' /etc/apt/sources.list && \
apt-get update && \
apt-get -y upgrade
In this step,we are installing Supervisor process manager and create folders for storing configuration
# supervisor installation &&
# create directory for child images to store configuration in
RUN apt-get -y install supervisor && \
  mkdir -p /var/log/supervisor && \
  mkdir -p /etc/supervisor/conf.d
Next is to copy the Angular application and Spring Boot executable to container
RUN mkdir /usr/api
WORKDIR /usr/api

# Add API Executable jar
COPY myapp-api-0.0.1-SNAPSHOT.jar /usr/api/app.jar

# Add Dist folder for Angular app
COPY dist/ /usr/local/apache2/htdocs/
COPY .htaccess /usr/local/apache2/htdocs/
RUN chmod -R 755 /usr/local/apache2/htdocs/
COPY httpd.conf /usr/local/apache2/conf/httpd.conf
Add Supervisor.conf base configuration file to container.Configuration file would have all child programs and their respective start commands,configuration etc.,
# supervisor base configuration 
ADD supervisor.conf /etc/supervisor.conf
Finally add command for docker’s init system
# default command
CMD ["supervisord", "-c", "/etc/supervisor.conf"]
Finally Docker file would now be looking like the one below
FROM rickw/ubuntu12-java8
#cleanup
RUN add-apt-repository -r ppa:webupd8team/java
RUN apt-get update && apt-get install -y apache2 && apt-get clean && rm -rf /var/lib/apt/lists/*
RUN \
  sed -i 's/# \(.*multiverse$\)/\1/g' /etc/apt/sources.list && \
  apt-get update && \
  apt-get -y upgrade

# supervisor installation &&
# create directory for child images to store configuration in
RUN apt-get -y install supervisor && \
  mkdir -p /var/log/supervisor && \
  mkdir -p /etc/supervisor/conf.d
RUN mkdir /usr/api
WORKDIR /usr/api

# Add API Executable jar
COPY myapp-api-0.0.1-SNAPSHOT.jar /usr/api/app.jar

# Add Dist folder
COPY dist/ /usr/local/apache2/htdocs/
COPY .htaccess /usr/local/apache2/htdocs/
RUN chmod -R 755 /usr/local/apache2/htdocs/
COPY httpd.conf /usr/local/apache2/conf/httpd.conf

# supervisor base configuration
ADD supervisor.conf /etc/supervisor.conf

# default command
CMD ["supervisord", "-c", "/etc/supervisor.conf"]
Supervisor configuration file
[supervisord]
nodaemon=true

[program:apache2]
command=service apache2 restart
killasgroup=true
stopasgroup=true
redirect_stderr=true

[program:springbootapp]
directory=/usr/api
command=/bin/bash -c "java -jar app.jar"
stdout_logfile=/var/log/supervisor/%(program_name)s.log
stderr_logfile=/var/log/supervisor/%(program_name)s.log
Step #3. Build Docker Image
Now that we have completed Dockerfile and also have Supervisor configuration file ready, next step is to build Docker image by docker build command

docker build -t orderapp .
Here -t specifies the name of the image.


Image — Docker build for running multiple process using Supervisor
Logs for both applications can be located at /var/log/supervisor/***.log

Congrats! We have successfully built container for our application with Supervisor process manager.

Step #4. Test Docker Image
To run the Docker image,execute the following commands

docker run -p 4200:4200 orderapp
Here -p specifies the port container:host mapping.

Launch your browser and hit http://localhost:4200 to access the application running on your container.

Image — Angular application served from container
Congrats! We have successfully built and run container for running multiple services using Supervisor.There is much more to the Docker platform than what was covered here, but now you would have got a good idea of the basics of building containers for an application.

