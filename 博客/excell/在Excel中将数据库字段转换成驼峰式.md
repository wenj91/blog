# 在Excel中将数据库字段转换成驼峰式

1.将数据库字段复制到Excel表格第一列；  
2.在第二列顶部输入=PROPER(A1)命令；  
3.在第三列顶部输入=SUBSTITUTE(B1,"_","")命令；  
4.在第四列顶部输入=LOWER(LEFT(C1,1))&RIGHT(C1,LEN(C1)-1)即可得到驼峰式字段  

---

版权声明：本文为CSDN博主「billxin0621」的原创文章，遵循CC 4.0 BY-SA版权协议，转载请附上原文出处链接及本声明。
原文链接：https://blog.csdn.net/billxin0621/article/details/88848947


LOWER(LEFT(F1,1))&RIGHT(F1,LEN(F1)-1)
