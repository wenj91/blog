# [Configure Kie Execution Server on WildFly](http://www.mastertheboss.com/jboss-jbpm/jbpm6/running-rules-on-wildfly-with-kie-server)

What is the KIE Execution Server? The Kie Server is a Java web application that allow us to expose rules and business process to be executed remotely using REST and JMS interfaces. The difference between Kie Server and jBPM Console is that Kie Server is focused in remote execution, while jBPM console offers a complete authoring environment, including process execution features and a remote API.

How it works? The Kie Server itself is a web application that can be deployed in JBoss EAP, Wildfly, tomcat and other Java application servers or web containers. It works by accessing kjars from a Maven repository and exposing its rules and process throught HTTP or JMS.

1
Maven Repository <- KIE SERVER  <-> Remote JMS/REST Clients
kie execution server wildfly

The important concepts behind the KIE Execution server for us today are:

Kie Server: execution server purely focusing on providing runtime environment for both rules and processes. These capabilities are provided by Kie Server Extensions. More capabilities can be added by further extensions (e.g. customer could add his own extensions in case of missing functionality that will then use infrastructure of the KIE Server). A Kie Server instance is a standalone Kie Server executing on a given application server/web container. A Kie Server instantiates and provides support for multiple Kie Containers.

Kie Server Extension: a "plugin" for the Kie Server that adds capabilities to the server. The Kie Server ships with two default kie server extensions: BRM and BPM.

Kie Container: an in-memory instantiation of a kjar, allowing for the instantiation and usage of its assets (domain models, processes, rules, etc). A Kie Server exposes Kie Containers through a standard API over transport protocols like REST and JMS.

Controller: a server-backed REST endpoint that will be responsible for managing KIE Server instances. Such end point must provide following capabilities:

respond to connect requests
sync all registered containers on the corresponding Kie Server ID
respond to disconnect requests
Kie Server state: currently known state of given Kie Server instance. This is a local storage (by default in file) that maintains the following information:

list of registered controllers
list of known containers
kie server configuration
The server state is persisted upon receival of events like: Kie Container created, Kie Container is disposed, controller accepts registration of Kie Server instance, etc.

Kie Server ID: an arbitrary assigned identifier to which configurations are assigned. At boot, each Kie Server Instance is assigned an ID, and that ID is matched to a configuration on the controller. The Kie Server Instance fetches and uses that configuration to setup itself.

This was took from KIE Server documentation. Notice that the concept of controller is similar to Widlfly/JBoss EAP domain mode, however, today we will use the server in unmanaged mode - it will make easy to achieve our goal, which is introduce how Kie Server works.

Step #1 Installing Kie Server on Wildfly 
Supposing the same server you will run the Kie Server application has Java 8 and Maven installed, the steps to install Kie Server on Widlfly 8.2 would be:

Download and unzip Wildfly Application server
Download and unzip Kie Server Execution Server
Copy the latest WAR file (e.g. kie-server-7.15.0.Final-ee7.war) into the deployments folder of WildFly. You can rename it to "kie-server.war" for your convenience.
Add an application user with the role kie-server using the add-user script:
1
 $ ./add-user.sh -a -u kieserver -p password1! -g admin,kie-server
Start wildfly using the standalone-full.xml profile and providing a few Kie server parameters:

1
$ standalone.sh  -c standalone-full.xml -Dorg.kie.server.id=hello-kie-server -Dorg.kie.server.location=http://localhost:8080/kie-server/services/rest/server
Test the installation using a browser or curl. Accessing the endpoint http://localhost:8080/kie-server/services/rest/server should return the main information about the Kie Server installation:

1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
<response type="SUCCESS" msg="Kie Server info">
   <kie-server-info>
      <capabilities>KieServer</capabilities>
      <capabilities>BRM</capabilities>
      <capabilities>BPM</capabilities>
      <capabilities>CaseMgmt</capabilities>
      <capabilities>BPM-UI</capabilities>
      <capabilities>BRP</capabilities>
      <capabilities>DMN</capabilities>
      <capabilities>Swagger</capabilities>
      <location>http://localhost:8280/kie-server/services/rest/server</location>
      <name>demo-server</name>
      <id>demo-server</id>
      <version>7.15.0.Final</version>
   </kie-server-info>
</response>
Step #2 Package a Maven artifact in a KJAR
Our Rules and Processes needs to be packaged in a special artifact type called KJAR.

What is a KJAR ? A KJAR file is a simple JAR file that include a descriptor for KIE system to produce KieBase and KieSession. Descriptor of the KJAR is represented as XML file.

For this article we will use the following Maven project which produces a KJAR file: https://github.com/jesuino/hello-kie-server . Here is the content of this project:

kie server tutorial jboss

And here is the content of each file:

pom.xml: It is the maven descriptor of our project:

1
2
3
4
5
6
7
8
9
10
<project xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/maven-v4_0_0.xsd">
  <modelVersion>4.0.0</modelVersion>
  <groupId>org.mastertheboss.kieserver</groupId>
  <artifactId>hello-kie-server</artifactId>
  <packaging>jar</packaging>
  <version>1.0</version>
  <name>hello-kie-server</name>
  <url>http://www.mastertheboss.com/</url>
</project>
kmodule.xml: kmodule is a XML file, the descriptor used in kjars.

1
2
<kmodule xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xmlns="http://jboss.org/kie/6.0.0/kmodule" />
hello.drl: A simple rule file that will be fired once a String is in the working memory:

1
2
3
4
5
6
rule 'hello'
when
    $name: String()
then
    System.out.println("Hello " + $name);
end
hello.bpmn2: it translate to the following simple process:

kie server tutorial jboss

The content of Say Hello Script script task is:

1
System.out.println("Hello World from process, " + name + "!");
name is a process variable that can be passed as parameter when starting the process. This project can be found at github at: https://github.com/jesuino/hello-kie-server

Step #3 Install the KJAR in a Maven repository
The simplest way to make available the KJAR to the Kie Execution Server is to install it on the local Maven repository. This can be done using the following maven command:

1
mvn clean install
Step #4 Create a Container for the KIE Server
Once your Execution Server is registered, you can start adding Kie Containers to it. Kie Containers are self contained environments that have been provisioned to hold instances of your packaged and deployed rule instance

To create the container we use the REST API by sending a PUT HTTP request to the endpoint http://localhost:8080/kie-server/services/rest/server/containers/hello, where hello is the name and the ID of the container. This request uses authentication(remember the user you added later?) and we must send the kjar artifact maven information, see:

1
$ curl -X PUT -H 'Content-type: application/xml' -u 'kieserver:password1!' --data @createHelloContainer.xml http://localhost:8080/kie-server/services/rest/server/containers/hello
The content of the file createHelloContainer.xml is:

1
2
3
4
5
6
7
8
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<kie-container container-id="hr">
    <release-id>
        <group-id>org.mastertheboss.kieserver</group-id>
        <artifact-id>hello-kie-server</artifact-id>
        <version>1.0</version>
    </release-id>
</kie-container>
Now that the container is created and started. We can see it in the server logs:

1
2
3
4
22:00:48,576 INFO  [org.drools.compiler.kie.builder.impl.KieRepositoryImpl] (EJB default - 1) KieModule was added: ZipKieModule[releaseId=org.mastertheboss.kieserver:hello-kie-server:1.0,file=/home/wsiqueir/.m2/repository/org/mastertheboss/kieserver/hello-kie-server/1.0/hello-kie-server-1.0.jar]
22:00:48,694 INFO  [org.kie.scanner.embedder.MavenEmbedderUtils] (EJB default - 1) Not in OSGi: using plexus based maven parser
22:00:49,378 INFO  [org.kie.server.services.jbpm.JbpmKieServerExtension] (EJB default - 1) Container hello created successfully
22:00:49,379 INFO  [org.kie.server.services.impl.KieServerImpl] (EJB default - 1) Container hello (for release id org.mastertheboss.kieserver:hello-kie-server:1.0) successfully started
The endpoint to send commands and interact with business process for the container hello is: http://localhost:8080/kie-server/services/rest/server/containers/instances/hello

Below you can see the details about the instances we created:

1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
$ curl -u 'kieserver:password1!'  -H 'Accept: application/json' 'http://localhost:8080/kie-server/services/rest/server/containers/hello' 
{
  "type" : "SUCCESS",
  "msg" : "Info for container hello",
  "result" : {
    "kie-container" : {
      "status" : "STARTED",
      "scanner" : {
        "status" : "DISPOSED",
        "poll-interval" : null
      },
      "container-id" : "hello",
      "release-id" : {
        "version" : "1.0",
        "group-id" : "org.mastertheboss.kieserver",
        "artifact-id" : "hello-kie-server"
      },
      "resolved-release-id" : {
        "version" : "1.0",
        "group-id" : "org.mastertheboss.kieserver",
        "artifact-id" : "hello-kie-server"
      }
    }
  }
}
Now we are ready to execute rules and business process using REST. The REST endpoint works by supportting JSON and XML formats currently - XML can be based on JAXB or XStream java XML frameworks.

Step #5 Let's test Business Rules
The first test, will be running the rule "Hello" which has been included in the KJAR file. In order to do that, we send requests with marshalled Drools commands to a single endpoint. In the command below, marshalled in JSON, we send two insert and a fire all rules command (actually they are inside a BatchExecutionCommand object):

1
2
3
4
5
6
7
{
  "commands" : [ 
    { "insert" : { "object" : "William"   } }, 
    { "insert" : { "object" : "Francesco" } }, 
    { "fire-all-rules" : { } }
  ]
}
To send the HTTP request we must include the HTTP header X-KIE-ContentType informing what data format we are using and now we use the method POST, see:

1
$ curl -X POST -H 'X-KIE-ContentType: JSON' -H 'Content-type: application/json' -u 'kieserver:password1!' --data @droolsCommands.json http://localhost:8080/kie-server/services/rest/server/containers/instances/hello
If you check the server logs, you can see that the rules were correctly fired:

1
2
21:10:11,095 INFO  [stdout] (default task-15) Hello Francesco
21:10:11,095 INFO  [stdout] (default task-15) Hello William
Step #6 Let's test Business Processes
Execute processes uses a REST API. We can start by querying the available processes definitions using the URL http://localhost:8080/kie-server/services/rest/server/queries/processes/definitions

You can see our business process was correctly installed in Kie Server. Now let’s start a process instance by using POST and JSON format on the following URL: http://localhost:8080/kie-server/services/rest/server/containers/hello/processes/hello/instances

1
$ curl -X POST -H 'Content-type: application/json' -H 'X-KIE-ContentType: JSON' -u 'kieserver:password1!' --data @startProcess.json http://localhost:8080/kie-server/services/rest/server/containers/hello/processes/hello/instances
The content of the file startProcess.json is a map to send the value of the process parameter name:

1
2
3
{
  "name" : "William"
}
It will return the process instance ID. In this case we have a simple process, but in other cases we can keep it to do other operations with the server. Anyway, once we send this request, we should be able to see in the server logs that the process was correct

1
21:35:40,841 INFO  [stdout] (default task-23) Hello World from process, William!
That’s all for process. Of course the API is a little more complex, since it includes signalling process, tasks management, variables, etc. But this is all for today.

Conclusion

In this article we introduced the Kie Server project. The source code of the sample kjar and the commands can be found at github (https://github.com/jesuino/hello-kie-server).

Of course there are a lot to learn, like how to use the API to manage tasks and jobs and explore the controller mode. Maciej has a good and complete article series about the Kie Server, you can refer to it if you want to learn more about it.

Next Steps
Learn how to deploy the Business Central on the top of WildFly to design, build and deploy your Drools and BPM assets: Getting started with Business Central Workbench

Author: William Antônio Siqueira has about 6 years of Java programming experience. He started with JavaFX since its first release and since then he follows all the JavaFX updates. He regularly blogs about JavaFX, JBoss and Java application on fxapps.blogspot.com He also founded a JUG on his region which has about 100 members. Currently he works for Red Hat supporting and helping on JBoss products maintenance. His main areas of interest are Java EE, REST Web Services, jBPM and JavaFX.