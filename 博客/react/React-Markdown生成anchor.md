# [React-Markdown生成anchor](https://github.com/rexxars/react-markdown/issues/404)

I have some markup

1. [Top Level](#Top-Level)
   1. [Windows](#Windows)
   2. [OSX/Linux](#OSX/Linux)
   3. [Android](#Android)

## Top Level

## Windows

## OSX/Linux

## Android
In markup the table of conents links to the headers. The HTML generated creates the right links

<a href="#Windows">Windows</a>
but the headings aren't anchored. If they were

<h1><a >Windows</a></h1>

## 实现方法

```typescript
import React from 'react';

const flatten = (text: string, child) => {
  return typeof child === 'string'
    ? text + child
    : React.Children.toArray(child.props.children).reduce(flatten, text);
};

/**
 * HeadingRenderer is a custom renderer
 * It parses the heading and attaches an id to it to be used as an anchor
 */
const HeadingRenderer = props => {
  const children = React.Children.toArray(props.children);
  const text = children.reduce(flatten, '');
  const slug = text.toLowerCase().replace(/\W/g, '-');
  return React.createElement('h' + props.level, { id: slug }, props.children);
};

export default HeadingRenderer;
```

## 引用

```typescript
<ReactMarkdown
  source={markdown}
  escapeHtml={false}
  renderers={{heading: HeadingRenderer}}
/>
```