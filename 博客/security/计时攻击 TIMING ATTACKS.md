# [计时攻击 TIMING ATTACKS](https://coolshell.cn/articles/21003.html#%E8%AE%A1%E6%97%B6%E6%94%BB%E5%87%BB(Timing_Attack))

本文来自读者“程序猿石头”的投稿文章《这 10 行比较字符串相等的代码给我整懵了，不信你也来看看》，原文写的很好，但不够直接了当，信息密度不够高，所以我对原文进行大量的删减、裁剪、改写和添加，主要删除了一些没有信息的段落，主要加入了如何实施计时攻击相关的其它内容，让这篇文章中的知识更系统一些，并且还指出了其它的一些问题。所以，我把标题也改成了《计时攻击 Timing Attacks》。

目录  	
另类的字符串比较    
计时攻击(Timing Attack)  
各语言的对应函数  
One More Thing  


## 另类的字符串比较  

在 Java 的 Play Framework 里有一段代码用来验证cookie(session)中的数据是否合法（包含签名的验证）的代码，如下所示：

```java
boolean safeEqual(String a, String b) {
   if (a.length() != b.length()) {
       return false;
   }
   int equal = 0;
   for (int i = 0; i < a.length(); i++) {
       equal |= a.charAt(i) ^ b.charAt(i);
   }
   return equal == 0;
}
```

相信刚看到这段源码的人会感觉挺奇怪的，这个函数的功能是比较两个字符串是否相等，如果要判断两个字符串是否相等，正常人的写法应该是下面这个样子的（来自JDK8 的 String.equals()-有删减）：

```java
public boolean equals(Object anObject) {
    String anotherString = (String)anObject;
    int n = value.length;
    if (n == anotherString.value.length) {
        char v1[] = value;
        char v2[] = anotherString.value;
        int i = 0;
        while (n-- != 0) {
            if (v1[i] != v2[i]) // <- 遇到第一个不一样的字符时退出
                return false;
            i++;
        }
        return true;
    }
    return false;
}
```

我们可以看到，在比较两个字符串是否相等的正常写法是：

先看一下两个字符串长度是否相等，如果不等直接返回 false。
如果长度相等，则依次判断每个字符是否相等，如果不等则返回 false。
如果全部相等，则返回 true。一旦遇到不一样的字符时，直接返回false。
然而，Play Framework里的代码却不是这样的，尤其是上述的第2点，用到了异或，熟悉位操作的你很容易就能看懂，通过异或操作 1^1=0 , 1^0=1, 0^0=0，来比较每一位，如果每一位都相等的话，两个字符串肯定相等，最后存储累计异或值的变量 equal必定为 0（因为相同的字符必然为偶数），否则为 1。

但是，这种异或的方式不是遇到第一个不一样的字符就返回 false 了，而是要做全量比较，这种比较完全没有效率，这是为什么呢？原因是为了安全。

## 计时攻击(Timing Attack)

计时攻击（Wikipedia）是旁道攻击(或称”侧信道攻击”， Side Channel Attack， 简称SCA) 的一种，旁通道攻击是指基于从计算机系统的实现中获得的信息的任何攻击 ，而不是基于实现的算法本身的弱点（例如，密码分析和软件错误）。时间信息，功耗，电磁泄漏甚至声音可以提供额外的信息来源，可以加以利用。在很多物理隔绝的环境中（黑盒），往往也能出奇制胜，这类新型攻击的有效性远高于传统的密码分析的数学方法。（注：企图通过社会工程学欺骗或强迫具有合法访问权限的人来破坏密码系统通常不被视为旁道攻击）

计时攻击是最常用的攻击方法。那么，正常的字符串比较是怎么被黑客进行时间攻击的呢？

我们知道，正常的字符串比较，一旦遇到每一个不同的字符就返回失败了，所以，理论上来说，前面只有2个字符相同字符串比较的耗时，要比前面有10个字符相同的比较要短。你会说，这能相差多少呢？可能几微秒吧。但是，我们可以放大这个事。比如，在Web应用时，记录每个请求的返回所需请求时间（一般是毫秒级），如果我们重复50次，我们可以查看平均时间或是p50的时间，以了解哪个字符返回的时间比较长，如果某个我们要尝试的字符串的时间比较长，我们就可以确定地得出这个这字符串的前面一段必然是正确的。（当然，你会说网络请求的燥音太多了，在毫秒级的请求上完全没办判断，这个需要用到统计学来降噪，后面会给出方法）

这个事情，可以用来做HMAC的攻击，所谓HMAC，你可以参看本站的《HTTP API 认证授权术》文章了解更多的细节。简单来说，HMAC，就是客户端向服务端发来一个字符串和其签名字符串（HMAC），然后，服务端的程序用一个私钥来对客户端发来的字符串进行签名得到签名字符串，然后再比较这个签名字符串（所谓签名，也就是使用MD5或SHA这样的哈希算法进行编码，是不可逆的）

写成伪代码大概是这个样子：

```php
bool verify(message, digest) {
    my_digest = HMAC(key, message);
    return my_digest.equals(digest) ;
}
```

于是，攻击者在不知道签名算法和私钥的情况下，但是知道API的调用接口时，就可以通过一边穷举签名，一边统计调用时间的方式来非常有效率的破解签名。在这篇文章《HMAC Timing Attacks》中记录了整个攻击的过程。文章中记载：

如果一个签名有40个长度，如：f5acdffbf0bb39b2cdf59ccc19625015b33f55fe 攻击者，从 0000000000000000000000000000000000000000 开始穷举，下面是穷举第一个字符（从0到f因为这是HMAC算法的取值范围）的时间统计。

```
0 0.005450913
1 0.005829198
2 0.004905407
3 0.005286876
4 0.005597611
5 0.004814430
6 0.004969118
7 0.005335884
8 0.004433182
9 0.004440246
a 0.004860263
b 0.004561121
c 0.004463188
d 0.004406799
e 0.004978907
f 0.004887240
```

可以看到，第一次测试通过的计时结果（以秒为单位），而值“ f”与样品的其余部分之间没有较大的变化量，所有结果看起来都非常接近。换句话说，有很多噪声掩盖了信号。因此，有必要进行多个采样（对测试进行缩放）并使用统计工具从噪声中滤除信号。为了将信号与噪声分开，我们必须按任意常数对测试进行缩放。通过实验，作者发现500是一个很好的数字。换句话说：运行测试500次，并记录500个试验中每个试验的结果。然后，通过人的肉眼观察可以可能看到 f 的调用明显比别的要长，但是这种方法很难自动化。

所以，作者给了另一个统计算法，这个算法向服务器分别从 0 到 f 发出16个请求，并记录每个请求的响应时间，并将它们排序为1-16，其中1是最长（最慢）的请求，而16是最短（最快的请求），分别记录 0 – f 的名次，然后重复上述的过程 500 次。如下所示（仅显示25个样本，字符“ 0”首先被排名7、1、3，然后再次排名3……）：

```
{
"0"=>[7, 1, 3, 3, 15, 5, 4, 9, 15, 10, 13, 2, 14, 9, 4, 14, 7, 9, 15, 2, 14, 9, 14, 6, 11...],
"1"=>[13, 4, 7, 11, 0, 4, 0, 2, 14, 11, 6, 7, 2, 2, 14, 11, 8, 10, 5, 13, 11, 7, 4, 9, 3...],
"2"=>[14, 5, 15, 5, 1, 0, 3, 1, 9, 12, 4, 4, 1, 1, 8, 6, 9, 4, 9, 5, 8, 3, 12, 8, 5...],
"3"=>[15, 2, 9, 7, 2, 1, 14, 11, 7, 8, 8, 1, 4, 7, 12, 15, 13, 0, 4, 1, 7, 0, 3, 0, 0...],
"4"=>[12, 10, 14, 15, 8, 9, 10, 12, 10, 4, 1, 13, 15, 15, 3, 1, 6, 8, 2, 6, 15, 4, 0, 3, 2...],
"5"=>[5, 13, 13, 12, 7, 8, 13, 14, 3, 13, 2, 12, 7, 14, 2, 10, 12, 5, 8, 0, 4, 10, 5, 10, 12...]
"6"=>[0, 15, 11, 13, 5, 15, 8, 8, 4, 7, 12, 9, 10, 11, 11, 7, 0, 6, 0, 9, 2, 6, 15, 13, 14...]
"7"=>[1, 9, 0, 10, 6, 6, 2, 4, 12, 9, 5, 10, 5, 10, 7, 2, 4, 14, 6, 7, 13, 11, 6, 12, 4...],
"8"=>[4, 0, 2, 1, 9, 11, 12, 13, 11, 14, 0, 15, 9, 0, 0, 13, 11, 13, 1, 8, 6, 5, 11, 15, 7...],
"9"=>[11, 11, 10, 4, 13, 7, 6, 3, 2, 2, 14, 5, 3, 3, 15, 9, 14, 7, 10, 3, 0, 14, 1, 5, 15...],
"a"=>[8, 3, 6, 14, 10, 2, 7, 5, 1, 3, 3, 0, 0, 6, 10, 12, 15, 12, 12, 15, 9, 13, 13, 11, 9...],
"b"=>[9, 12, 5, 8, 3, 3, 5, 15, 0, 6, 11, 11, 12, 8, 1, 3, 1, 11, 11, 14, 5, 1, 2, 1, 6...],
"c"=>[6, 7, 8, 2, 12, 10, 9, 10, 6, 1, 10, 8, 6, 4, 6, 4, 3, 2, 7, 11, 1, 8, 7, 2, 13...],
"d"=>[2, 14, 4, 0, 14, 12, 11, 0, 8, 0, 15, 3, 8, 12, 5, 0, 10, 1, 3, 4, 12, 12, 8, 14, 8...],
"e"=>[10, 8, 12, 6, 11, 13, 1, 6, 13, 5, 7, 14, 11, 5, 9, 5, 2, 15, 14, 10, 10, 2, 10, 4, 1...],
"f"=>[3, 6, 1, 9, 4, 14, 15, 7, 5, 15, 9, 6, 13, 13, 13, 8, 5, 3, 13, 12, 3, 15, 9, 7, 10...]
}
```

然后将每个字符的500个排名进行平均，得出以下示例输出：

```
"f", 5.302
"0", 7.17
"6", 7.396
"3", 7.472
"5", 7.562
"a", 7.602
"2", 7.608
"8", 7.626
"9", 7.688
"b", 7.698
"1", 7.704
"e", 7.812
"4", 7.82
"d", 7.826
"7", 7.854
"c", 7.86
```

于是，f 就这样脱颖而出了。然后，再对剩余的39个字符重复此算法。

这是一种统计技术，可让我们从噪声中滤出真实的信号。因此，总共需要调用：16 * 500 * 40 = 320,000个请求，而蛮力穷举需要花费16 ^ 40个请求。

另外，学术界的这篇论文就宣称用这种计时攻击的方法破解了 OpenSSL 0.9.7 的RSA加密算法了。这篇 Remote Timing Attacks are Practical （PDF）论文中指出（我大致翻译下摘要，感兴趣的同学可以通过链接去看原文）：

计时攻击往往用于攻击一些性能较弱的计算设备，例如一些智能卡。我们通过实验发现，也能用于攻击普通的软件系统。本文通过实验证明，通过这种计时攻击方式能够攻破一个基于 OpenSSL 的 web 服务器的私钥。结果证明计时攻击用于进行网络攻击在实践中可行的，因此各大安全系统需要抵御这种风险。

## 参考资料：

Timing Attacks on RSA: Revealing Your Secrets through the Fourth Dimension
Remote Timing Attacks are Practical

## 各语言的对应函数

下面，我们来看看各个语言对计时攻击的对应函数

PHP: https://wiki.php.net/rfc/timing_attack

```
bool hash_equals ( string $known_string , string $user_string )
boolean password_verify ( string $password , string $hash )
```

Java:  Java 在1.6时是有问题的，其在 1.6.0._17(6u17)才Fix了这个问题（相关的fix patch），下面是 JDK8源码 – MessageDigest.isEqual()

```java
public static boolean MessageDigest.isEqual(byte[] digesta, byte[] digestb) {
    if (digesta == digestb) return true;
    if (digesta == null || digestb == null) {
        return false;
    }
    if (digesta.length != digestb.length) {
        return false;
    }
    int result = 0;
    // time-constant comparison
    for (int i = 0; i < digesta.length; i++) {
        result |= digesta[i] ^ digestb[i];
    }
    return result == 0;
}
```

C/C++：没有在常用的库中找到相关的函数，还是自己写吧。

```cpp
int util_cmp_const(const void * a, const void *b, const size_t size) 
{
  const unsigned char *_a = (const unsigned char *) a;
  const unsigned char *_b = (const unsigned char *) b;
  unsigned char result = 0;
  size_t i;
  for (i = 0; i < size; i++) {
    result |= _a[i] ^ _b[i];
  }
  return result; /* returns 0 if equal, nonzero otherwise */
}
```

Python – 2.7.7+使用 hmac.compare_digest(a, b)，否则，使用如下的Django的代码

```python
#Taken from Django Source Code
def constant_time_compare(val1, val2):
    """
    Returns True if the two strings are equal, False otherwise.
    The time taken is independent of the number of characters that match.
    For the sake of simplicity, this function executes in constant time only
    when the two strings have the same length. It short-circuits when they
    have different lengths.
    """
    if len(val1) != len(val2):
        return False
    result = 0
    for x, y in zip(val1, val2):
        result |= ord(x) ^ ord(y)
    return result == 0
```

Go  – 使用 crypto/subtle 代码包

```golang
func ConstantTimeByteEq(x, y uint8) int
func ConstantTimeCompare(x, y []byte) int
func ConstantTimeCopy(v int, x, y []byte)
func ConstantTimeEq(x, y int32) int
func ConstantTimeLessOrEq(x, y int) int
func ConstantTimeSelect(v, x, y int) int
```

## One More Thing
在文章结束前，再提一个事。

上面的所有的代码都还有一个问题——他们都要判断字符串的长度是否一致，如果不一致就返回了，所以，通过时间攻击是可以知道字符串的长度的。比如：你的密码长度。理论上来说，字符串的长度也应该属于“隐私数据”（当然，对于签名则不是）。

(全文完)
