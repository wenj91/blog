# [Maven自定义Parent并集成SpringBoot](https://perkins4j2.github.io/posts/53117/)

       

Maven自定义Parent并集成SpringBoot
===========================

发表于 2019-10-12 | 分类于 [工具利器](https://perkins4j2.github.io/categories/工具利器/) | 阅读次数：

本文字数： 1.3k | 阅读时长 ≈ 1 分钟

### [](# "自定义Parent")自定义Parent

1  
2  
3  
4  
5  
6  

<modelVersion\>4.0.0</modelVersion\>  
  
<groupId\>com.xx.xx</groupId\>  
<artifactId\>xx-parent</artifactId\>  
<version\>1.0.1</version\>  
<packaging\>pom</packaging\>  

### [](# "parent依赖管理集成SpringBoot")parent依赖管理集成SpringBoot

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

<dependencyManagement>  
 <dependencies>  
 <dependency>  
 <groupId>org.springframework.boot</groupId>  
 <artifactId>spring-boot-starter-parent</artifactId>  
 <version>2.0.5.RELEASE</version>  
 <type>pom</type>  
 <scope>import</scope>  
 </dependency>  
 </dependencies>  
 </dependencyManagement>  

### [](# "子项目继承和使用")子项目继承和使用

#### [](# "继承Parent")继承Parent

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

<modelVersion\>4.0.0</modelVersion\>  
  
<artifactId\>xx-xx</artifactId\>  
<version\>1.0-SNAPSHOT</version\>  
  
<parent\>  
 <artifactId\>xx-xx</artifactId\>  
 <groupId\>com.xx.xx</groupId\>  
 <version\>1.0.1</version\>  
</parent\>  

#### [](# "使用SpringBoot")使用SpringBoot

1  
2  
3  
4  
5  
6  

<dependencies\>  
 <dependency\>   
 <groupId\>org.springframework.boot</groupId\>   
 <artifactId\>spring-boot-starter-web</artifactId\>   
 </dependency\>   
</dependencies\>  

相关文章

*   [Maven 打包Plugins集成](https://perkins4j2.github.io/posts/22633/)
    
*   [Maven基本操作](https://perkins4j2.github.io/posts/725/)
    
*   [Maven多Module自定义archetype](https://perkins4j2.github.io/posts/72500/)
    
*   [Maven自定义打包脚本](https://perkins4j2.github.io/posts/26061/)
    
*   [Maven配置Profile](https://perkins4j2.github.io/posts/52371/)
    

\------ 本文结束------

本文标题:Maven自定义Parent并集成SpringBoot

文章作者:Perkins

发布时间:2019年10月12日

原始链接:[https://perkins4j2.github.io/posts/53117/](https://perkins4j2.github.io/posts/53117/ "Maven自定义Parent并集成SpringBoot")

许可协议: [署名-非商业性使用-禁止演绎 4.0 国际](https://creativecommons.org/licenses/by-nc-nd/4.0/ "Attribution-NonCommercial-NoDerivatives 4.0 International (CC BY-NC-ND 4.0)") 转载请保留原文链接及作者。

var clipboard = new Clipboard('.fa-clipboard'); clipboard.on('success', $(function(){ $(".fa-clipboard").click(function(){ swal({ title: "", text: '复制成功', html: false, timer: 500, showConfirmButton: false }); }); }));

[\# Maven](https://perkins4j2.github.io/tags/Maven/)

[SaaS多租户数据库方案](/posts/20124/ "SaaS多租户数据库方案")

[SpringBoot+JPA实体自动生成数据库](/posts/4871/ "SpringBoot+JPA实体自动生成数据库")