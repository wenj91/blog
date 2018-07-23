# dubbo集成zipkin
`Zipkin`是`Twitter`的一个开源项目，是一个致力于收集`Twitter`所有服务的监控数据的分布式跟踪系统，它提供了收集数据，和查询数据两大接口服务。

## zipkin服务端
将构建与启动一个运行在本地的Zipkin实例。有三种方式：使用`JAVA`、`Docker`或`源码`。
如果你对`Docker`很熟悉，下面是首选的启动方式。如果你对`Docker`不熟悉，可以通过`java`或`源码`启动。

### Docker
可以通过[Docker Zipkin](https://github.com/openzipkin/docker-zipkin)项目自行构建`docker`镜像, 该项目为预构建提供了`scripts`脚本还有`docker-compose.yml`配置, 以帮助用户快速构建最新的`docker`镜像和启动服务.

`docker run -d -p 9411:9411 openzipkin/zipkin`

### Java
如果安装了`Java 8`及以上版本，启动项目最快的方式是获取`latest release`最新的版本作为独立的可执行jar
```bash
wget -O zipkin.jar 'https://search.maven.org/remote_content?g=io.zipkin.java&a=zipkin-server&v=LATEST&c=exec'
java -jar zipkin.jar
```

获取源码自行构建运行
如果你想探索`Zipkin`的新特性，`Zipkin`也支持以源码方式运行。该方式需要你获取`Zipkin源码`并且编译它。

* Get the latest source(获取最新源码)
`git clone https://github.com/openzipkin/zipkin`
`cd zipkin`
* Build the server and also make its dependencies(构建打包)
`./mvnw -DskipTests --also-make -pl zipkin-server clean install`
* Run the server(运行)
`java -jar ./zipkin-server/target/zipkin-server-*exec.jar`

至此zipkin服务端已经成功运行, 不管如何启动Zipkin的, 打开浏览器`http://your_host:9411`就可以看到追踪。

## zipkin客户端集成
### zipkin依赖
在pom.xml文件添加如下依赖
```xml
<properties>
    <brave.version>4.19.2</brave.version>
    <zipkin-reporter.version>2.1.3</zipkin-reporter.version>
    <zipkin.version>2.8.1</zipkin.version>
</properties>
<dependencies>
    <dependency>
        <groupId>io.zipkin.reporter2</groupId>
        <artifactId>zipkin-reporter</artifactId>
        <version>2.6.0</version>
        <type>pom</type>
    </dependency>
    <dependency>
        <groupId>io.zipkin.reporter2</groupId>
        <artifactId>zipkin-sender-okhttp3</artifactId>
        <version>2.6.0</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.brave</groupId>
        <artifactId>brave</artifactId>
        <version>4.19.2</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.brave</groupId>
        <artifactId>brave-context-log4j2</artifactId>
        <version>4.19.2</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.brave</groupId>
        <artifactId>brave-instrumentation-http</artifactId>
        <version>4.19.2</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.brave</groupId>
        <artifactId>brave-instrumentation-http-tests</artifactId>
        <version>4.19.2</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.brave</groupId>
        <artifactId>brave-instrumentation-servlet</artifactId>
        <version>4.19.2</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.brave</groupId>
        <artifactId>brave-tests</artifactId>
        <version>4.19.2</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.zipkin2</groupId>
        <artifactId>zipkin</artifactId>
        <version>${zipkin.version}</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.java</groupId>
        <artifactId>zipkin</artifactId>
        <version>${zipkin.version}</version>
    </dependency>
    <dependency>
        <groupId>io.zipkin.java</groupId>
        <artifactId>zipkin-junit</artifactId>
        <version>${zipkin.version}</version>
    </dependency>
</dependencies>
```

### zipkin tracing配置
```java
package com.github.wenj91.dubbo.zipkin.config;


import brave.Tracing;
import brave.servlet.TracingFilter;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.boot.web.servlet.FilterRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import zipkin2.Span;
import zipkin2.reporter.AsyncReporter;
import zipkin2.reporter.Reporter;
import zipkin2.reporter.Sender;
import zipkin2.reporter.okhttp3.OkHttpSender;

import javax.servlet.Filter;

@Configuration
public class TracingConfig {

    /**
     * 配置zipkin服务地址
     */
    @Value("${zipkin.tracing.endpoint:http://localhost:9411/api/v2/spans}")
    private String zipkinEndPoint;

    @Value("${zipkin.tracing.local-service-name:local-service-name}")
    private String localServiceName;

    /**
     * 配置sender
     * @return
     */
    @Bean
    public Sender sender(){
        OkHttpSender sender = OkHttpSender
                .newBuilder()
                .endpoint(zipkinEndPoint)
                .build();
        return sender;
    }

    /**
     * 配置reporter
     * @param sender
     * @return
     */
    @Bean
    public Reporter<Span> reporter(Sender sender){
       return AsyncReporter
               .builder(sender)
               .build();
    }

    /**
     * 配置dubbo-consumer tracing
     * @param reporter
     * @return
     */
    @Bean
    public Tracing tracing(Reporter reporter){
       return Tracing.newBuilder()
                .localServiceName(localServiceName)
                .spanReporter(reporter)
                .build();
    }

    /**
     * 配置http tracing
     * @param reporter
     * @return
     */
    @Bean
    public Tracing tracing2(Reporter reporter){
        return Tracing.newBuilder()
                .localServiceName(localServiceName + "_http")
                .spanReporter(reporter)
                .build();
    }

    /**
     * 配置servlet filter
     * @param tracing2
     * @return
     */
    @Bean
    public Filter filter(Tracing tracing2){
        return TracingFilter.create(tracing2);
    }

    /**
     * 注册filter
     * @param filter
     * @return
     */
    @Bean
    public FilterRegistrationBean filterRegistration(Filter filter) {
        FilterRegistrationBean registration = new FilterRegistrationBean();
        registration.setFilter(filter);
        registration.addUrlPatterns("/*");
        registration.setName("zipkin-filter");
        registration.setOrder(1);
        return registration;
    }
}

```
启用自动配置
在`resources`目录新建`META-INF`目录:
新建文件:`spring.factories`
```properties
org.springframework.boot.autoconfigure.EnableAutoConfiguration=\
  com.github.wenj91.dubbo.zipkin.config.TracingConfig
```

### ZipkinHelper
ZipkinHelper主要用于实现span的构建并上报
```java
package com.github.wenj91.dubbo.zipkin.filter;

import brave.Span;
import brave.Tracer;
import brave.propagation.Propagation;
import com.alibaba.dubbo.remoting.exchange.ResponseCallback;
import com.alibaba.dubbo.rpc.*;
import com.alibaba.dubbo.rpc.protocol.dubbo.FutureAdapter;
import com.alibaba.dubbo.rpc.support.RpcUtils;
import com.alibaba.fastjson.JSON;
import zipkin2.Endpoint;

import java.net.InetSocketAddress;
import java.util.Map;
import java.util.concurrent.Future;

/**
 * @author wenj91
 * @Description:
 * @date 2018/6/22 10:44
 */
public class ZipkinHelper {

    static final Propagation.Setter<Map<String, String>, String> SETTER =
            new Propagation.Setter<Map<String, String>, String>() {
                @Override
                public void put(Map<String, String> carrier, String key, String value) {
                    carrier.put(key, value);
                }
                @Override
                public String toString() {
                    return JSON.toJSONString(this);
                }
            };
    static final Propagation.Getter<Map<String, String>, String> GETTER =
            new Propagation.Getter<Map<String, String>, String>() {
                @Override
                public String get(Map<String, String> carrier, String key) {
                    return carrier.get(key);
                }

                @Override
                public String toString() {
                    return JSON.toJSONString(this);
                }
            };

    static void buildSpan(Span span, Span.Kind kind, InetSocketAddress remoteAddress, String service, String method){
        if (!span.isNoop()) {
            span.kind(kind).start();
            span.kind(kind);
            span.name(service + "/" + method);
            Endpoint.Builder remoteEndpoint = Endpoint.newBuilder().port(remoteAddress.getPort());
            if (!remoteEndpoint.parseIp(remoteAddress.getAddress())) {
                remoteEndpoint.parseIp(remoteAddress.getHostName());
            }
            span.remoteEndpoint(remoteEndpoint.build());
        }
    }

    static Result spanTracing(Span span, Tracer tracer, Invoker<?> invoker, Invocation invocation, RpcContext rpcContext){
        boolean isOneway = false;
        boolean deferFinish = false;
        try (Tracer.SpanInScope scope = tracer.withSpanInScope(span)) {
            Result result = invoker.invoke(invocation);
            if (result.hasException()) {
                onError(result.getException(), span);
            }
            isOneway = RpcUtils.isOneway(invoker.getUrl(), invocation);
            Future<Object> future = rpcContext.getFuture(); // the case on async client invocation
            if (future instanceof FutureAdapter) {
                deferFinish = true;
                ((FutureAdapter) future).getFuture().setCallback(new ZipkinHelper.FinishSpanCallback(span));
            }
            return result;
        } catch (Exception e) {
            onError(e, span);
            throw e;
        } finally {
            if (isOneway) {
                span.flush();
            } else if (!deferFinish) {
                span.finish();
            }
        }
    }

    static void onError(Throwable error, Span span) {
        span.error(error);
        if (error instanceof RpcException) {
            span.tag("dubbo.error_code", Integer.toString(((RpcException) error).getCode()));
        }
    }

    static final class FinishSpanCallback implements ResponseCallback {
        final Span span;

        FinishSpanCallback(Span span) {
            this.span = span;
        }

        @Override public void done(Object response) {
            span.finish();
        }

        @Override public void caught(Throwable exception) {
            onError(exception, span);
            span.finish();
        }
    }
}
```

### DubboZipkinConsumerFilter
DubboZipkinConsumerFilter是dubbo消费端过滤器,用于拦截zipkin上下文消息
```java
package com.github.wenj91.dubbo.zipkin.filter;

import brave.Span;
import brave.Tracer;
import brave.Tracing;
import brave.propagation.TraceContext;
import com.alibaba.dubbo.common.Constants;
import com.alibaba.dubbo.common.extension.Activate;
import com.alibaba.dubbo.config.spring.extension.SpringExtensionFactory;
import com.alibaba.dubbo.rpc.*;
import com.alibaba.dubbo.rpc.support.RpcUtils;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.Map;

import static com.github.wenj91.dubbo.zipkin.filter.ZipkinHelper.SETTER;

@Activate(group = Constants.CONSUMER)
public class DubboZipkinConsumerFilter implements Filter {
    private static final Logger log = LoggerFactory.getLogger(DubboZipkinConsumerFilter.class);

    private SpringExtensionFactory springExtensionFactory = new SpringExtensionFactory();
    private Tracer tracer;

    // tracing上下文消息注入
    private TraceContext.Injector<Map<String, String>> injector;

    @Override
    public Result invoke(Invoker<?> invoker, Invocation invocation) throws RpcException {
        log.info("dubbo zipkin consumer filter......");

        Tracing tracing = springExtensionFactory.getExtension(Tracing.class, "tracing");
        tracer = tracing.tracer();
        if (tracer == null){
            return invoker.invoke(invocation);
        }

        if (null == injector){
            injector = tracing.propagation().injector(SETTER);
        }

        RpcContext rpcContext = RpcContext.getContext();
        Span span = tracer.nextSpan();
        injector.inject(span.context(), invocation.getAttachments());

        ZipkinHelper.buildSpan(span, Span.Kind.CONSUMER, rpcContext.getRemoteAddress(), invoker.getInterface().getSimpleName(),
                RpcUtils.getMethodName(invocation));

        return ZipkinHelper.spanTracing(span, tracer, invoker, invocation, rpcContext);
    }

}
```
### DubboZipkinProviderFilter
DubboZipkinProviderFilter是dubbo服务端过滤器,用于拦截zipkin上下文消息
```java
package com.github.wenj91.dubbo.zipkin.filter;

import brave.Span;
import brave.Tracer;
import brave.Tracing;
import brave.propagation.TraceContext;
import brave.propagation.TraceContextOrSamplingFlags;
import com.alibaba.dubbo.common.Constants;
import com.alibaba.dubbo.common.extension.Activate;
import com.alibaba.dubbo.config.spring.extension.SpringExtensionFactory;
import com.alibaba.dubbo.rpc.*;
import com.alibaba.dubbo.rpc.support.RpcUtils;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.Map;

import static com.github.wenj91.dubbo.zipkin.filter.ZipkinHelper.*;

@Activate(group = Constants.PROVIDER)
public class DubboZipkinProviderFilter implements Filter {

    private static final Logger log = LoggerFactory.getLogger(DubboZipkinProviderFilter.class);

    private SpringExtensionFactory springExtensionFactory = new SpringExtensionFactory();
    private Tracer tracer;

    // tracing上下文消息提取
    private TraceContext.Extractor<Map<String, String>> extractor;

    @Override
    public Result invoke(Invoker<?> invoker, Invocation invocation) throws RpcException {
        log.info("dubbo zipkin provider filter......");

        Tracing tracing = springExtensionFactory.getExtension(Tracing.class, "tracing");
        tracer = tracing.tracer();
        if (null == tracer){
            return invoker.invoke(invocation);
        }

        if (null == extractor){
            extractor = tracing.propagation().extractor(GETTER);
        }

        TraceContextOrSamplingFlags extracted = extractor.extract(invocation.getAttachments());
        Span span = extracted.context() != null
                ? tracer.joinSpan(extracted.context())
                : tracer.nextSpan(extracted);

        RpcContext rpcContext = RpcContext.getContext();
        ZipkinHelper.buildSpan(span, Span.Kind.SERVER, rpcContext.getRemoteAddress(), invoker.getInterface().getSimpleName(),
                RpcUtils.getMethodName(invocation));

        return ZipkinHelper.spanTracing(span, tracer, invoker, invocation, rpcContext);
    }
}

```
在`META-INF`添加`dubbo`文件夹,
新建文件:`com.alibaba.dubbo.rpc.Filter`
文件内容:
```
zipkinConsumerDubboFilter=com.github.wenj91.dubbo.zipkin.filter.DubboZipkinConsumerFilter
zipkinProviderDubboFilter=com.github.wenj91.dubbo.zipkin.filter.DubboZipkinProviderFilter
```

### spring boot相关配置
dubbo provider添加配置:
```yml
zipkin:
  tracing:
    local-service-name: zipkin-provider
```

dubbo consumer添加配置:
```yml
zipkin:
  tracing:
    local-service-name: zipkin-consumer
```
