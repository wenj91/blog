# Install Elasticsearch with Docker

[转载自elk官方文档](https://www.elastic.co/guide/en/elasticsearch/reference/current/docker.html#docker)

Elasticsearch is also available as Docker images. The images use centos:7 as the base image.

A list of all published Docker images and tags is available at www.docker.elastic.co. The source code is in GitHub.

These images are free to use under the Elastic license. They contain open source and free commercial features and access to paid commercial features. Start a 30-day trial to try out all of the paid commercial features. See the Subscriptions page for information about Elastic license levels.

## Pulling the imageedit
Obtaining Elasticsearch for Docker is as simple as issuing a docker pull command against the Elastic Docker registry.

`docker pull docker.elastic.co/elasticsearch/elasticsearch:6.5.1`  
Alternatively, you can download other Docker images that contain only features available under the Apache 2.0 license. To download the images, go to www.docker.elastic.co.

## Running Elasticsearch from the command line
### Development mode
Elasticsearch can be quickly started for development or testing use with the following command:

`docker run -p 9200:9200 -p 9300:9300 -e "discovery.type=single-node" docker.elastic.co/elasticsearch/elasticsearch:6.5.1`
### Production mode
Important  
The vm.max_map_count kernel setting needs to be set to at least 262144 for production use. Depending on your platform:

#### Linux

The vm.max_map_count setting should be set permanently in /etc/sysctl.conf:

```
$ grep vm.max_map_count /etc/sysctl.conf
vm.max_map_count=262144
To apply the setting on a live system type: sysctl -w vm.max_map_count=262144
```

#### macOS with Docker for Mac

The vm.max_map_count setting must be set within the xhyve virtual machine:

```
$ screen ~/Library/Containers/com.docker.docker/Data/vms/0/tty
```
Just press enter and configure the sysctl setting as you would for Linux:

sysctl -w vm.max_map_count=262144
Windows and macOS with Docker Toolbox

The vm.max_map_count setting must be set via docker-machine:
```
docker-machine ssh
sudo sysctl -w vm.max_map_count=262144
```

The following example brings up a cluster comprising two Elasticsearch nodes. To bring up the cluster, use the docker-compose.yml and just type:
`docker-compose up`  
Note
docker-compose is not pre-installed with Docker on Linux. Instructions for installing it can be found on the Docker Compose webpage.

The node elasticsearch listens on localhost:9200 while elasticsearch2 talks to elasticsearch over a Docker network.

This example also uses Docker named volumes, called esdata1 and esdata2 which will be created if not already present.

docker-compose.yml:
```yaml
version: '2.2'
services:
  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:6.5.1
    container_name: elasticsearch
    environment:
      - cluster.name=docker-cluster
      - bootstrap.memory_lock=true
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    ulimits:
      memlock:
        soft: -1
        hard: -1
    volumes:
      - esdata1:/usr/share/elasticsearch/data
    ports:
      - 9200:9200
    networks:
      - esnet
  elasticsearch2:
    image: docker.elastic.co/elasticsearch/elasticsearch:6.5.1
    container_name: elasticsearch2
    environment:
      - cluster.name=docker-cluster
      - bootstrap.memory_lock=true
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
      - "discovery.zen.ping.unicast.hosts=elasticsearch"
    ulimits:
      memlock:
        soft: -1
        hard: -1
    volumes:
      - esdata2:/usr/share/elasticsearch/data
    networks:
      - esnet

volumes:
  esdata1:
    driver: local
  esdata2:
    driver: local

networks:
  esnet:
```
To stop the cluster, type docker-compose down. Data volumes will persist, so it’s possible to start the cluster again with the same data using docker-compose up`. To destroy the cluster and the data volumes, just type docker-compose down -v.

Inspect status of cluster:  
curl http://127.0.0.1:9200/_cat/health  
1472225929 15:38:49 docker-cluster green 2 2 4 2 0 0 0 0 - 100.0%  

Log messages go to the console and are handled by the configured Docker logging driver. By default you can access logs with docker logs.

Configuring Elasticsearch with Docker
Elasticsearch loads its configuration from files under /usr/share/elasticsearch/config/. These configuration files are documented in Configuring Elasticsearch and Setting JVM options.

The image offers several methods for configuring Elasticsearch settings with the conventional approach being to provide customized files, that is to say, elasticsearch.yml. It’s also possible to use environment variables to set options:

A. Present the parameters via Docker environment variablesedit
For example, to define the cluster name with docker run you can pass -e "cluster.name=mynewclustername". Double quotes are required.

B. Bind-mounted configurationedit
Create your custom config file and mount this over the image’s corresponding file. For example, bind-mounting a custom_elasticsearch.yml with docker run can be accomplished with the parameter:

-v full_path_to/custom_elasticsearch.yml:/usr/share/elasticsearch/config/elasticsearch.yml
Important
The container runs Elasticsearch as user elasticsearch using uid:gid 1000:1000. Bind mounted host directories and files, such as custom_elasticsearch.yml above, need to be accessible by this user. For the data and log dirs, such as /usr/share/elasticsearch/data, write access is required as well. Also see note 1 below.

C. Customized image
In some environments, it may make more sense to prepare a custom image containing your configuration. A Dockerfile to achieve this may be as simple as:

FROM docker.elastic.co/elasticsearch/elasticsearch:6.5.1
COPY --chown=elasticsearch:elasticsearch elasticsearch.yml /usr/share/elasticsearch/config/  
You could then build and try the image with something like:

```
docker build --tag=elasticsearch-custom .
docker run -ti -v /usr/share/elasticsearch/data elasticsearch-custom
```

Some plugins require additional security permissions. You have to explicitly accept them either by attaching a tty when you run the Docker image and accepting yes at the prompts, or inspecting the security permissions separately and if you are comfortable with them adding the --batch flag to the plugin install command. See Plugin Management documentation for more details.

D. Override the image’s default CMDedit
Options can be passed as command-line options to the Elasticsearch process by overriding the default command for the image. For example:

docker run <various parameters> bin/elasticsearch -Ecluster.name=mynewclustername
Configuring SSL/TLS with the Elasticsearch Docker imageedit
See Encrypting Communications in an Elasticsearch Docker Container.

Notes for production use and defaultsedit
We have collected a number of best practices for production use. Any Docker parameters mentioned below assume the use of docker run.

By default, Elasticsearch runs inside the container as user elasticsearch using uid:gid 1000:1000.

Caution
One exception is Openshift which runs containers using an arbitrarily assigned user ID. Openshift will present persistent volumes with the gid set to 0 which will work without any adjustments.

If you are bind-mounting a local directory or file, ensure it is readable by this user, while the data and log dirs additionally require write access. A good strategy is to grant group access to gid 1000 or 0 for the local directory. As an example, to prepare a local directory for storing data through a bind-mount:

mkdir esdatadir
chmod g+rwx esdatadir
chgrp 1000 esdatadir
As a last resort, you can also force the container to mutate the ownership of any bind-mounts used for the data and log dirs through the environment variable TAKE_FILE_OWNERSHIP; in this case they will be owned by uid:gid 1000:0 providing read/write access to the Elasticsearch process as required.

It is important to ensure increased ulimits for nofile and nproc are available for the Elasticsearch containers. Verify the init system for the Docker daemon is already setting those to acceptable values and, if needed, adjust them in the Daemon, or override them per container, for example using docker run:

--ulimit nofile=65536:65536
Note
One way of checking the Docker daemon defaults for the aforementioned ulimits is by running:

docker run --rm centos:7 /bin/bash -c 'ulimit -Hn && ulimit -Sn && ulimit -Hu && ulimit -Su'
Swapping needs to be disabled for performance and node stability. This can be achieved through any of the methods mentioned in the Elasticsearch docs. If you opt for the bootstrap.memory_lock: true approach, apart from defining it through any of the configuration methods, you will additionally need the memlock: true ulimit, either defined in the Docker Daemon or specifically set for the container. This is demonstrated above in the docker-compose.yml. If using docker run:

-e "bootstrap.memory_lock=true" --ulimit memlock=-1:-1
The image exposes TCP ports 9200 and 9300. For clusters it is recommended to randomize the published ports with --publish-all, unless you are pinning one container per host.
Use the ES_JAVA_OPTS environment variable to set heap size. For example, to use 16GB use -e ES_JAVA_OPTS="-Xms16g -Xmx16g" with docker run.
Pin your deployments to a specific version of the Elasticsearch Docker image. For example, docker.elastic.co/elasticsearch/elasticsearch:6.5.1.
Always use a volume bound on /usr/share/elasticsearch/data, as shown in the production example, for the following reasons:

The data of your elasticsearch node won’t be lost if the container is killed
Elasticsearch is I/O sensitive and the Docker storage driver is not ideal for fast I/O
It allows the use of advanced Docker volume plugins
If you are using the devicemapper storage driver, make sure you are not using the default loop-lvm mode. Configure docker-engine to use direct-lvm instead.
Consider centralizing your logs by using a different logging driver. Also note that the default json-file logging driver is not ideally suited for production use.
Next stepsedit
You now have a test Elasticsearch environment set up. Before you start serious development or go into production with Elasticsearch, you must do some additional setup:

Learn how to configure Elasticsearch.
Configure important Elasticsearch settings.
Configure important system settings.
«  Install Elasticsearch with Windows MSI Installer     Configuring Elasticsearch  »
Getting Started Videos
Starting Elasticsearch

