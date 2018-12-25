# base image
FROM centos
# MAINTAINER
MAINTAINER wenj91@163.com

ADD mycat  /usr/local/mycat 

RUN yum install java-1.8.0-openjdk.x86_64


EXPOSE 8066 9066 3306
RUN chmod -R 777 /usr/local/mycat/bin  
CMD ["./usr/local/mycat/bin/mycat", "console"] 


docker build -t mycat:0.0.1 .       #创建镜像
docker run --name mycat -v /Users/wenj91/docker/mycat/mycat/conf:/usr/local/mycat/conf -v /Users/wenj91/docker/mycat/mycat/logs:/usr/local/mycat/logs -p 8066:8066 -p 9066:9066 -p 3316:3306 mycat:0.0.1   

