## dubbo异常拦截(ExceptionFilter)

在一些业务场景, 往往需要自定义异常来满足特定的业务, 在抛出异常后, 通过捕获还有`instanceof`来判断是否是自定义异常, 然后做特定的业务处理.  

在`dubbo`调用中, 当`provider`抛出异常后, `consumer`是否可以通过上述方法来达到业务需求了呢?  

目前dubbo已知拓展`com.alibaba.dubbo.rpc.filter.ExceptionFilter`就是专门处理异常的.  
下面通过`ExceptionFilter`源码来看看它是怎么处理的:
```java

    // filter拦截方法
    public Result invoke(Invoker<?> invoker, Invocation invocation) throws RpcException {
        try {
            Result result = invoker.invoke(invocation);

            // 判断调用结果是否存在异常
            if (result.hasException() && GenericService.class != invoker.getInterface()) {
                try {
                    Throwable exception = result.getException();

                    // (如果是checked异常则直接抛异常)directly throw if it's checked exception
                    if (!(exception instanceof RuntimeException) && (exception instanceof Exception)) {
                        return result;
                    }
                    // (如果是接口方法声明的异常则直接抛异常)directly throw if the exception appears in the signature
                    try {
                        Method method = invoker.getInterface().getMethod(invocation.getMethodName(), invocation.getParameterTypes());
                        Class<?>[] exceptionClassses = method.getExceptionTypes();
                        for (Class<?> exceptionClass : exceptionClassses) {
                            if (exception.getClass().equals(exceptionClass)) {
                                return result;
                            }
                        }
                    } catch (NoSuchMethodException e) {
                        return result;
                    }

                    // (如果在方法声明中没有发现这个异常, 则在日志中以error级别打印这个异常)for the exception not found in method's signature, print ERROR message in server's log.
                    logger.error("Got unchecked and undeclared exception which called by " + RpcContext.getContext().getRemoteHost()
                            + ". service: " + invoker.getInterface().getName() + ", method: " + invocation.getMethodName()
                            + ", exception: " + exception.getClass().getName() + ": " + exception.getMessage(), exception);

                    // (如果这个异常与接口在同一个jar包中 则直接抛异常)directly throw if exception class and interface class are in the same jar file.
                    String serviceFile = ReflectUtils.getCodeBase(invoker.getInterface());
                    String exceptionFile = ReflectUtils.getCodeBase(exception.getClass());
                    if (serviceFile == null || exceptionFile == null || serviceFile.equals(exceptionFile)) {
                        return result;
                    }
                    // (如果是jdk异常则直接抛异常)directly throw if it's JDK exception
                    String className = exception.getClass().getName();
                    if (className.startsWith("java.") || className.startsWith("javax.")) {
                        return result;
                    }
                    // (如果是dubbo的异常则直接抛异常)directly throw if it's dubbo exception
                    if (exception instanceof RpcException) {
                        return result;
                    }

                    // (否则, 将异常堆栈信息转化成string, 然后以RuntimeException异常形式抛出来)otherwise, wrap with RuntimeException and throw back to the client
                    return new RpcResult(new RuntimeException(StringUtils.toString(exception)));
                } catch (Throwable e) {
                    logger.warn("Fail to ExceptionFilter when called by " + RpcContext.getContext().getRemoteHost()
                            + ". service: " + invoker.getInterface().getName() + ", method: " + invocation.getMethodName()
                            + ", exception: " + e.getClass().getName() + ": " + e.getMessage(), e);
                    return result;
                }
            }
            return result;
        } catch (RuntimeException e) {
            logger.error("Got unchecked and undeclared exception which called by " + RpcContext.getContext().getRemoteHost()
                    + ". service: " + invoker.getInterface().getName() + ", method: " + invocation.getMethodName()
                    + ", exception: " + e.getClass().getName() + ": " + e.getMessage(), e);
            throw e;
        }
    }
```

总结下来, 有几点:
1. `checked`异常
2. 接口方法声明的异常
3. 异常与接口在同一个`jar`包中
4. `jdk`异常
5. `dubbo`的异常

以上几种异常,`dubbo`会直接抛出异常,
其他情况都会将异常堆栈信息转化成`String`, 然后以`RuntimeException`异常形式抛出来
