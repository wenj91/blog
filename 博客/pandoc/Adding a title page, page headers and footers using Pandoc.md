# [Adding a title page, page headers and footers using Pandoc](https://stackoverrun.com/cn/q/5293968)

How do you specify that Pandoc should use a specific header and footer when generating a PDF from Markdown?

Currently I use the following to create my doc from the command line:

    pandoc -s -V geometry:margin=1in --number-sections -o doc.pdf doc.mkd

This gives a lovely result with numbered sections.

What I would like to do, is to include a header and footer on each page. How do I go about it?

The [extensions mechanism](http://johnmacfarlane.net/pandoc/demo/example9/pandocs-markdown.html), e.g. `pandoc_title_block` may hold the key, but how do you use it?

Lastly (as a bonus), is there some way to create a title page for a document using pandoc?

pandoc8,451

[来源](https://stackoverflow.com/q/19397100) [分享](https://stackoverrun.com/cn/q/5293968)

创建 16 10月. 132013-10-16 07:02:40 [Jack](https://stackoverflow.com/users/828757/)

 0

A good answer to this question can be found here, too [https://tex.stackexchange.com/a/139205/134508](https://tex.stackexchange.com/a/139205/134508) – [Aᴄʜᴇʀᴏɴғᴀɪʟ](https://stackoverflow.com/users/5552584/) 13 8月. 172017-08-13 22:21:52

When using the `-s` (`--standalone`) argument for the generation of PDFs, Pandoc uses a specific LateX template.

You can look at the template with `pandoc -D latex` and notice that there are many variables that you can set (the same way you did you with `-V geometry:margin=1in` in your command).

In order to add a specific header/footer, you can modify this template and use the `--template` argument in the command line.

You can get more information here [Pandoc demo templates](http://johnmacfarlane.net/pandoc/demo/example9/templates.html)

[来源](https://stackoverflow.com/q/19620699) [创建 27 10月. 132013-10-27 16:54:14](https://stackoverrun.com/cn/q/5293968>分享</a></small>
</p>
<p class=) [garnierclement](https://stackoverflow.com/users/2028639/)

 0

For others information: The "geometry" variable is not implemented in pandoc included in Ubuntu 12.04 LTS. For a possible workaround see: [](https://groups.google.com/forum/>https://groups.google.com/forum/#!topic/pandoc-discuss/59mBEixlIJU</a></span> – <span class=)[Samuel Åslund](https://stackoverflow.com/users/671282/) 18 3月. 142014-03-18 09:49:11