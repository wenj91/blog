# [react-markdown 扩展规则](https://innei.ren/posts/programming/react-markdown-custom-rules)

## react-markdown 扩展规则
上帝说，要有光，于是便有了光
为了 Markdown 更加具有可玩性，一般我们无法满足于标准的 Markdown 语法，所以有了 GFM (GitHub Flavored Markdown)，这是 GitHub 扩展 Markdown 语法的规范。但是如果这也无法满足我们的需求呢？那么就需要我们自己来定制了。

开始之前
首先需要安装如下几个库
yarn add react-markdown remark-parse
至于需要 react 之类的话，就不必多说了。此文章基于 react-markdown 库进行定制 markdown 语法。
简单使用
react-markdown 的使用方法非常简单，只需要这样就行了。

```typescript
import ReactMarkdown, { ReactMarkdownProps } from 'react-markdown';
const Markdown: FC = props => {
  return <ReactMarkdown>
    {/* value 为 markdown 内容 */}
  	{props.value}
  </ReactMarkdown>
}
```

一般的，到这里其实就 ok 了。
如果你不满足于此，那么进入今天的主题。
定制语法
spoiler 是一个新的语法，token 为 ||这是文字||，通过两个竖线包裹，被渲染为文字和背景同色，鼠标移上后使背景透明。类似萌娘的剧透内容的样式。
!防剧透内容

扩展之前，我们首先要知道 react-markdown 是对 remark 的一次封装，所以是可以使用 remark 的插件来扩展语法的。那么接下来我们就来做一个插件。

```typescript
// rules/spoiler.ts
import { Eat, Parser } from 'remark-parse';

function tokenizeSpoiler(eat: Eat, value: string, silent?: boolean): any {
  const match = /\|\|(.*)\|\|/.exec(value);

  if (match) {
    if (silent) {
      return true;
    }
    try {
      return eat(match[0])({
        type: 'spoiler',
        value: match[1]
      });
    } catch {
      // console.log(match[0]);
    }
  }
}
tokenizeSpoiler.notInLink = true;
tokenizeSpoiler.locator = function(value, fromIndex) {
  return value.indexOf('||', fromIndex);
};

function spoilerSyntax(this: any) {
  const Parser = this.Parser as { prototype: Parser };
  const tokenizers = Parser.prototype.inlineTokenizers;
  const methods = Parser.prototype.inlineMethods;

  // Add an inline tokenizer (defined in the following example).
  tokenizers.spoiler = tokenizeSpoiler;

  // Run it just before `text`.
  methods.splice(methods.indexOf('text'), 0, 'spoiler');
}
export { spoilerSyntax };

// index.tsx
// import ...
const RenderSpoiler: FC<{ value: string }> = props => {
  return <span className={styles['spoiler']}>{props.value}</span>;
};

const Markdown = props => {
  return <ReactMarkdown plugins={[spoilerSyntax]} 
           renderers={{
        					spoiler: RenderSpoiler
    			}}
           >
  	{props.value}
  </ReactMarkdown>
}
```

以上的代码就完成了一个插件的开发，是不是特别简单呢。
你说你看不懂？没事，慢慢来。
首先，react-markdown 支持传入 plugins，为一个数组。数组里每个元素是一个函数，值得注意的是这个函数中的 this 是有值的，所以不要习惯用箭头函数了。

```typescript
function spoilerSyntax(this: any) { // 插件入口函数
  const Parser = this.Parser as { prototype: Parser };
  const tokenizers = Parser.prototype.inlineTokenizers;
  const methods = Parser.prototype.inlineMethods; // 获取所有的 inline types 的渲染顺序 是一个数组

  tokenizers.spoiler = tokenizeSpoiler; // 把我们定义的渲染器挂载到上面
	// spoiler 为 name，如果是自定义规则，那么这个 name 和下面的 第三个参数 应相同
  
  methods.splice(methods.indexOf('text'), 0, 'spoiler'); // 把定义的规则放在哪个顺序执行呢，就放在 `text` 之前吧。`text` 也是一个规则，在整个渲染的最后一个
  
}
```

那么这就是入口函数了，接下来来看 tokenizeSpoiler 函数， 这个是定义如何解析的函数。

```typescript
function tokenizeSpoiler(eat: Eat, value: string, silent?: boolean): any {
  const match = /\|\|(.*)\|\|/.exec(value); // 通过正则匹配字符串， value 是这一行的字符串

  if (match) {
    if (silent) { // 这个我也不知道干嘛用的，没用过，可以省略
      return true;
    }
    try { // 多吃可能导致 crash， 需要 catch
      return eat(match[0])({
        type: 'spoiler', // 自定义类型，必须在入口函数注册该名称，或使用内置名称
        value: match[1]
      });
    } catch {
      // console.log(match[0]);
    }
  }
}
// 内联规格必须制定一个定位器，以保证性能。一般是规则前缀
tokenizeSpoiler.locator = function(value, fromIndex) {
  return value.indexOf('||', fromIndex);
};
```

主要说一下 eat 函数，这个名字起得有点奇怪，不过理解之后就感觉起得很生动。
这是一个柯里化 (Currying) 函数，传入一个字符串，一般是匹配到的字符串，返回一个函数，该函数是你对上一个传入的字符串，做何种解析，需要传一个对象。相当于前一个函数是把原字符串（待解析）的传入串吃掉了，后一个就是这么吐出来的过程。除了type 是必须的，其他的任意，你可以传入任意 key-value，都会在渲染的时候暴露出来。
回到 Markdown 组件。

```typescript
// index.tsx
// import ...
import styles from './index.module.scss';
import ReactMarkdown, { ReactMarkdownProps } from 'react-markdown';

// 这个 value 就是之前 eat 传入的对象中的 value，在这里暴露出来了
const RenderSpoiler: FC<{ value: string }> = props => { 
  // 可以写点 styles 装饰一下？当然可以！
  return <span className={styles['spoiler']}>{props.value}</span>;
};

const Markdown = props => {
  return <ReactMarkdown plugins={[spoilerSyntax]} // 这个插件就是刚刚写得导出项
           renderers={{ // 为 spoiler 指定 renderer
        					spoiler: RenderSpoiler
    			}}
           >
  	{props.value}
  </ReactMarkdown>
}
```

```css
// index.module.scss
.spoiler {
    background-color: currentColor;
    transition: background 0.5s;
    &:hover {
      background-color: transparent;
    }
 }
 ```

到此为止，一个简单的规则就完成了。是不是很简单呢。
定义多个
很多情况我们不止于只定义单个规则，既然多个，就需要封装。
这里给一个示例代码，之后有时间再详讲。

```typescript
interface defineNewinLineSyntaxProps {
  regexp: RegExp;
  type: string;
  name?: string;
  locator: string | Locator;
  render?: ({ value: string }) => JSX.Element | null;
  handler?: (
    eat: Eat,
    type: string,
    value: string,
    matched: RegExpExecArray | null
  ) => object;
}

export function defineNewinLineSyntax({
  regexp,
  type,
  locator,
  render,
  handler,
  name
}: defineNewinLineSyntaxProps) {
  function tokenize(eat: Eat, value: string, silent?: boolean): any {
    const match = regexp.exec(value);

    if (match) {
      if (silent) {
        return true;
      }
      try {
        return (
          handler?.(eat, type, value, match) ??
          eat(match[0])({
            type,
            value: match[1],
            component: render?.({ value: match[1] as string }) ?? null
          })
        );
      } catch {
        // console.log(match[0]);
      }
    }
  }
  tokenize.notInLink = true;
  tokenize.locator =
    typeof locator === 'function'
      ? locator
      : function(value, fromIndex) {
          return value.indexOf(locator, fromIndex);
        };

  return function(this: any) {
    const Parser = this.Parser as { prototype: Parser };
    const tokenizers = Parser.prototype.inlineTokenizers;
    const methods = Parser.prototype.inlineMethods;

    tokenizers[name ?? type] = tokenize;

    methods.splice(methods.indexOf('text'), 0, name ?? type);
  };
}

```