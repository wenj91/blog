# jshint配置 文件说明

```json
{
    "maxerr"        : 99,       // {int} Maximum error before stopping 最大出错数


    // Enforcing
    "bitwise"       : false,     // true: Prohibit bitwise operators (&, |, ^, etc.)  是否允许一元运算符
    "camelcase"     : true,    // true: Identifiers must be in camelCase 变量名必须驼峰形式
    "curly"         : true,     // true: Require {} for every new block or scope 当if、while 后面逻辑不是表达式的时候，必须用 {} 来套住
    "eqeqeq"        : false,     // true: Require triple equals (===) for comparison  一定要强制用 === 比较？ 
    "forin"         : true,     // true: Require filtering for..in loops with obj.hasOwnProperty()  forin必须是自身属性
    "freeze"        : true,     // true: prohibits overwriting prototypes of native objects such as Array, Date etc.  修改原生对象会报错
    "immed"         : false,    // true: Require immediate invocations to be wrapped in parens e.g. `(function () { } ());`  自执行匿名函数必须套一对``
    "latedef"       : false,    // true: Require variables/functions to be defined before being used  是否允许函数声明提升
    "newcap"        : true,    // true: Require capitalization of all constructor functions e.g. `new F()`  所有的new操作符声明的对象必须大些开头，他们会被认为是构造函数
    "noarg"         : true,     // true: Prohibit use of `arguments.caller` and `arguments.callee`  禁用caller和callee方法
    "noempty"       : true,     // true: Prohibit use of empty blocks 不能有空块
    "nonbsp"        : true,     // true: Prohibit "non-breaking whitespace" characters. 不能有空格字符
    "nonew"         : true,    // true: Prohibit use of constructors for side-effects (without assignment) 不能声明new 构造函数而不赋值
    "plusplus"      : false,    // true: Prohibit use of `++` and `--`  允许是用++ --运算符
    "quotmark"      : single,    // Quotation mark consistency:  统一全局引号模式
                                //   false    : do nothing (default)
                                //   true     : ensure whatever is used is consistent
                                //   "single" : require single quotes
                                //   "double" : require double quotes
    "undef"         : true,     // true: Require all non-global variables to be declared (prevents global leaks)  禁止使用未申明的变量
    "unused"        : strict,     // Unused variables:  没用到的变量和方法是否要报错
                                //   true     : all variables, last function parameter
                                //   "vars"   : all variables only
                                //   "strict" : all variables, all function parameters
    "strict"        : true,     // true: Requires all functions run in ES5 Strict Mode  在严格模式下跑
    "maxparams"     : false,    // {int} Max number of formal params allowed per function  最大参数个数
    "maxdepth"      : false,    // {int} Max depth of nested blocks (within functions)  最大嵌套深度
    "maxstatements" : false,    // {int} Max number statements per function  最大表达式个数（在一个方法里）
    "maxcomplexity" : false,    // {int} Max cyclomatic complexity per function 一个函数最大循环数量
    "maxlen"        : false,    // {int} Max number of characters per line 单行最多字节数
    "varstmt"       : false,    // true: Disallow any var statements. Only `let` and `const` are allowed. 禁用var 


    // Relaxing
    "asi"           : false,     // true: Tolerate Automatic Semicolon Insertion (no semicolons)  容忍分号的缺少
    "boss"          : false,     // true: Tolerate assignments where comparisons would be expected 错字？
    "debug"         : false,     // true: Allow debugger statements e.g. browser breakpoints.  允许debugger语句？
    "eqnull"        : true,      // true: Tolerate use of `== null` 允许对null和未定义进行 == 比较？
    "esversion"     : 5,         // {int} Specify the ECMAScript version to which the code must adhere.  遵循那个js版本
    "moz"           : false,     // true: Allow Mozilla specific syntax (extends and overrides esnext features)  针对moz浏览器进行特定的调试？ 一般是不要的
                                 // (ex: `for each`, multiple try/catch, function expression…)
    "evil"          : false,     // true: Tolerate use of `eval` and `new Function()`   要容忍evil这个运算符？
    "expr"          : true,     // true: Tolerate `ExpressionStatement` as Programs  容忍函数表达式？
    "funcscope"     : false,     // true: Tolerate defining variables inside control statements   容忍块级别作用域 类似于let 和 var的区别
    "globalstrict"  : false,     // true: Allow global "use strict" (also enables 'strict')  全局都要严格模式？这个如果你全局用严格，可能别的组件有风险
    "iterator"      : false,     // true: Tolerate using the `__iterator__` property   容忍属性迭代？  不理解
    "lastsemic"     : true,     // true: Tolerate omitting a semicolon for the last statement of a 1-line block  容忍单行表达式可以只用最后一个分号
    "laxbreak"      : false,     // true: Tolerate possibly unsafe line breakings   容忍不安全和意外的终止
    "laxcomma"      : false,     // true: Tolerate comma-first style coding  容忍都还优先的代码风格
    "loopfunc"      : false,     // true: Tolerate functions being defined in loops   容忍在循环中可能出现的变量不符合预期
    "multistr"      : true,     // true: Tolerate multi-line strings 容忍多行字符串用\ 来拼接
    "noyield"       : false,     // true: Tolerate generator functions with no yield statement in them.  不懂
    "notypeof"      : false,     // true: Tolerate invalid typeof operator values  容忍无效的比值操作？ 一般false
    "proto"         : false,     // true: Tolerate using the `__proto__` property  容忍使用__proto__ 属性？
    "scripturl"     : false,     // true: Tolerate script-targeted URLs   不懂
    "shadow"        : false,     // true: Allows re-define variables later in code e.g. `var x=1; x=2;`  如果申明 脱离了当前作用域会报错
    "sub"           : false,     // true: Tolerate using `[]` notation when it can still be expressed in dot notation  当可以用.来取的时候，如果用[]来get会报错
    "supernew"      : false,     // true: Tolerate `new function () { ... };` and `new Object;`  容忍奇怪的构造
    "validthis"     : false,     // true: Tolerate using this in a non-constructor function  不懂


    // Environments
    "browser"       : true,     // Web Browser (window, document, etc) 
    "browserify"    : false,    // Browserify (node.js code in the browser)
    "couch"         : false,    // CouchDB
    "devel"         : true,     // Development/debugging (alert, confirm, etc)
    "dojo"          : false,    // Dojo Toolkit
    "jasmine"       : false,    // Jasmine
    "jquery"        : true,    // jQuery
    "mocha"         : false,     // Mocha
    "mootools"      : false,    // MooTools
    "node"          : false,    // Node.js
    "nonstandard"   : false,    // Widely adopted globals (escape, unescape, etc)
    "phantom"       : false,    // PhantomJS
    "prototypejs"   : false,    // Prototype and Scriptaculous
    "qunit"         : false,    // QUnit
    "rhino"         : false,    // Rhino
    "shelljs"       : false,    // ShellJS
    "typed"         : false,    // Globals for typed array constructions
    "worker"        : false,    // Web Workers
    "wsh"           : false,    // Windows Scripting Host
    "yui"           : false,    // Yahoo User Interface


    // Custom Globals
    "globals"       : {}        // additional predefined global variables  添加预定义的全局变量
}
```
--------------------- 
作者：yanyang1116 
来源：CSDN 
原文：https://blog.csdn.net/yanyang1116/article/details/70157519 
版权声明：本文为博主原创文章，转载请附上博文链接！