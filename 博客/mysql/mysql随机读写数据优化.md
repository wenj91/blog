MySql 随机读取数据
============

发布时间：[2009 年 4 月 14 日](http://ourmysql.com/archives/524 "下午 2:31") 发布者： [OurMySQL](http://ourmysql.com/archives/author/ourmysql "查看OurMySQL的文章")  
来源：[牡丹网景](http://www.wbphp.cn/html/y2009/1336.html "点击文章来源")   才被阅读：14,311 次 

一直以为mysql随机查询几条数据，就用

> SELECT \* FROM \`table\` ORDER BY RAND() LIMIT 5

就可以了。

但是真正测试一下才发现这样效率非常低。一个15万余条的库，查询5条数据，居然要8秒以上

查看官方手册，也说rand()放在ORDER BY 子句中会被执行多次，自然效率及很低。

> You cannot use a column with RAND() values in an ORDER BY clause, because ORDER BY would evaluate the column multiple times.

搜索Google，网上基本上都是查询max(id) \* rand()来随机获取数据。

> SELECT \*FROM \`table\` AS t1 JOIN (SELECT ROUND(RAND() \* (SELECT MAX(id) FROM \`table\`)) AS id) AS t2  
> WHERE t1.id >= t2.id  
> ORDER BY t1.id ASC LIMIT 5;

但是这样会产生连续的5条记录。解决办法只能是每次查询一条，查询5次。即便如此也值得，因为15万条的表，查询只需要0.01秒不到。

上面的语句采用的是JOIN，mysql的论坛上有人使用

> SELECT \*FROM \`table\`  
> WHERE id >= (SELECT FLOOR( MAX(id) \* RAND()) FROM \`table\` )  
> ORDER BY id LIMIT 1;

我测试了一下，需要0.5秒，速度也不错，但是跟上面的语句还是有很大差距。总觉有什么地方不正常。

于是我把语句改写了一下。

> SELECT \* FROM \`table\`  
> WHERE id >= (SELECT floor(RAND() \* (SELECT MAX(id) FROM \`table\`)))  
> ORDER BY id LIMIT 1;

这下，效率又提高了，查询时间只有0.01秒

最后，再把语句完善一下，加上MIN(id)的判断。我在最开始测试的时候，就是因为没有加上MIN(id)的判断，结果有一半的时间总是查询到表中的前面几行。  
完整查询语句是：

> SELECT \* FROM \`table\`  
> WHERE id >= (SELECT floor( RAND() \* ((SELECT MAX(id) FROM \`table\`)\-(SELECT MIN(id) FROM \`table\`)) + (SELECT MIN(id) FROM \`table\`)))  
> ORDER BY id LIMIT 1;
> 
> SELECT \*FROM \`table\` AS t1 JOIN (SELECT ROUND(RAND() \* ((SELECT MAX(id) FROM \`table\`)\-(SELECT MIN(id) FROM \`table\`))+(SELECT MIN(id) FROM \`table\`)) AS id) AS t2  
> WHERE t1.id >= t2.id  
> ORDER BY t1.id LIMIT 1;

最后在php中对这两个语句进行分别查询10次，  
前者花费时间 0.147433 秒  
后者花费时间 0.015130 秒  
看来采用JOIN的语法比直接在WHERE中使用函数效率还要高很多。