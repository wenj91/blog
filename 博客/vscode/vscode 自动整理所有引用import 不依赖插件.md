# [vscode 自动整理所有引用import 不依赖插件](https://blog.csdn.net/Synup/article/details/97498823)

settings.json中加入配置项： "editor.codeActionsOnSave": { "source.organizeImports": true },

可实现保存代码时（ctrl+s）自动整理页面所有import引用，包括删除未使用到的import、多import归类整理、排序等。很好用。
