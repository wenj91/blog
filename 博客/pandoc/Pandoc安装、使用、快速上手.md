# [Pandoc安装、使用、快速上手](https://blog.csdn.net/valada/article/details/104217597)

如果你需要将文档从一种格式转换成另一种格式，那么 Pandoc 是你的一把瑞士军刀，Pandoc 可以将下列格式文档进行相互转换。

> 如果你需要将文档从一种格式转换成另一种格式，那么 Pandoc 是你的一把瑞士军刀，Pandoc 可以将下列格式文档进行相互转换。 `Markdown`、`Microsoft Word`、`OpenOffice/LibreOffice`、`Jupyter notebook`、`HTML`、`EPUB`、`roff man`、`LaTeX`、甚至是`PDF`。当然 Pandoc 还包括很多类型文档的转换，这里就不一一例举了，可以参考[About pandoc](https://www.pandoc.org/index.html)。

安装
==

windows
-------

下载地址：[windows 下载](https://github.com/jgm/pandoc/releases/latest)也可以通过[Chocolatey](https://chocolatey.org/)来安装。

    choco install pandoc# 其他 Pandoc 软件安装choco install rsvg-convert python miktex

macOS
-----

    brew install pandoc# Pandoc 解析器brew install pandoc-citeproc# 其他 Pandoc 软件安装brew install librsvg python homebrew/cask/basictex

Linux
-----

    sudo yum install pandoc# 如果想输出 PDF，可以安装 TeX Livesudo yum install texlive

Ubuntu/Debian
-------------

    sudo apt install pandoc# 如果想输出 PDF，可以安装 TeX Livesudo apt install texlive

使用
==

> 经过上面的安装，我们可以使用了。
> 
> 验证
> --

    [frank@LAPTOP-0OCJTGJR ~]$ pandoc --versionpandoc 1.12.3.1Compiled with texmath 0.6.6, highlighting-kate 0.5.6.Syntax highlighting is supported for the following languages:    actionscript, ada, apache, asn1, asp, awk, bash, bibtex, boo, c, changelog,    clojure, cmake, coffee, coldfusion, commonlisp, cpp, cs, css, curry, d,    diff, djangotemplate, doxygen, doxygenlua, dtd, eiffel, email, erlang,    fortran, fsharp, gnuassembler, go, haskell, haxe, html, ini, java, javadoc,    javascript, json, jsp, julia, latex, lex, literatecurry, literatehaskell,    lua, makefile, mandoc, markdown, matlab, maxima, metafont, mips, modelines,    modula2, modula3, monobasic, nasm, noweb, objectivec, objectivecpp, ocaml,    octave, pascal, perl, php, pike, postscript, prolog, python, r,    relaxngcompact, restructuredtext, rhtml, roff, ruby, rust, scala, scheme,    sci, sed, sgml, sql, sqlmysql, sqlpostgresql, tcl, texinfo, verilog, vhdl,    xml, xorg, xslt, xul, yacc, yamlDefault user data directory: /home/frank/.pandocCopyright (C) 2006-2013 John MacFarlaneWeb:  http://johnmacfarlane.net/pandocThis is free software; see the source for copying conditions.  There is nowarranty, not even for merchantability or fitness for a particular purpose.

小试牛刀
----

> 虽然下面的方法不怎么常用，我们先通过命令行的一个简单例子认识一下 Pandoc

### 例子一：markdown 转 html

    [frank@LAPTOP-0OCJTGJR pandoc]$ pandoc# 标题一## 标题二> 摘要

这是按下 Ctrl-D，看看会发生什么？

    <h1 id="标题一">标题一</h1><h2 id="标题二">标题二</h2><blockquote><p>摘要</p></blockquote>

### 例子二：html 转 LaTeX

    [frank@LAPTOP-0OCJTGJR pandoc]$ pandoc -f html -t markdown<h1 id="标题一">标题一</h1><h2 id="标题二">标题二</h2><blockquote><p>摘要</p></blockquote>

这时再次按下 Ctrl-D，看看会发生了什么？

    标题一======标题二------> 摘要

我们是不是可以将 markdown 转换成 LaTeX 了？你会怎么使用命令组合呢？

文本转换
----

> 好了，玩够了，我们正式开始`文本转换`。
> 
> ### 编辑文档
> 
> 使用任意你喜欢的文本编辑器编辑如下文档并保持为.md 文件。这里保持成 test1.md

    ---title: Test...# Test!This is a test of *pandoc*.- list one- list two

### markdown 转 html

    [frank@LAPTOP-0OCJTGJR pandoc]$ pandoc test1.md -f markdown -t html -s -o test1.html[frank@LAPTOP-0OCJTGJR pandoc]$ lltotal 4-rw-rw-r-- 1 frank frank 629 Feb  4 22:06 test1.html-rw-rw-r-- 1 frank frank  81 Feb  4 22:05 test1.md

介绍一下参数-s 选项表示创建一个“独立”文件，其中包含页眉和页脚，而不仅仅是片段-o test1.html 将输出放入文件中 test1.html-f markdown 和-t html 表示，from markdown 格式 to html 格式，Pandoc 模式就是从 markdown 转成 html。

### markdown 转 LaTeX

    [frank@LAPTOP-0OCJTGJR pandoc]$ pandoc test1.md -f markdown -t latex -s -o test1.tex[frank@LAPTOP-0OCJTGJR pandoc]$ lltotal 8-rw-rw-r-- 1 frank frank  629 Feb  4 22:06 test1.html-rw-rw-r-- 1 frank frank   81 Feb  4 22:05 test1.md-rw-rw-r-- 1 frank frank 1627 Feb  4 22:17 test1.tex

当然，你也可以直接用下面的命令

    [frank@LAPTOP-0OCJTGJR pandoc]$ pandoc test1.md -s -o test2.tex[frank@LAPTOP-0OCJTGJR pandoc]$ lltotal 12-rw-rw-r-- 1 frank frank  629 Feb  4 22:06 test1.html-rw-rw-r-- 1 frank frank   81 Feb  4 22:05 test1.md-rw-rw-r-- 1 frank frank 1627 Feb  4 22:17 test1.tex-rw-rw-r-- 1 frank frank 1627 Feb  4 22:18 test2.tex

以为你的文件后缀是.tex，那么 Pandoc 就会知道你是想生成 LaTeX 文档，`够贴心`。

### markdown 转 pdf

如果想转成 pdf 那么我们需要安装 texlive 这个工具包，在`安装`一节中我们已经做过这件事儿了。那么我们就直接运行吧。

    [frank@LAPTOP-0OCJTGJR pandoc]$ pandoc test1.md -s -o test1.pdf[frank@LAPTOP-0OCJTGJR pandoc]$ lltotal 92-rw-rw-r-- 1 frank frank   629 Feb  4 22:06 test1.html-rw-rw-r-- 1 frank frank    81 Feb  4 22:05 test1.md-rw-rw-r-- 1 frank frank 80599 Feb  4 22:20 test1.pdf-rw-rw-r-- 1 frank frank  1627 Feb  4 22:17 test1.tex-rw-rw-r-- 1 frank frank  1627 Feb  4 22:18 test2.tex

进阶
==

更高级的用法可以参考[《Pandoc 用户指南》](https://pandoc.org/MANUAL.html)

阅读全文: [http://gitbook.cn/gitchat/activity/5e3ce061b754045045a2789b](http://gitbook.cn/gitchat/activity/5e3ce061b754045045a2789b?utm_source=csdn_blog)

您还可以下载 CSDN 旗下精品原创内容社区 GitChat App ，阅读更多 GitChat 专享技术内容哦。

![FtooAtPSkEJwnW-9xkCLqSTRpBKX](https://images.gitbook.cn/FtooAtPSkEJwnW-9xkCLqSTRpBKX)