
# [Markdown in React and Custom Page Elements](https://blog.evantahler.com/markdown-in-react-and-custom-page-elements-fd9703709be4)


The React Markdown package is wonderful at this step. You can load in a Markdown file and React Markdown with generate the HTML.  
A few tips:  
We use Next.js. The way that Next.js handles hydration of pages from the server to the client wants to pass DATA and not HTML. This means that if were to render the markdown content on the server when doing a hot-reload of the page (i.e. navigation form another page to this page), the markdown HTML would not properly render. That’s why we parse the markdown at the componentDidMount stage of the lifecycle. This may have adverse effects on the SEO of those pages.  
You can load the markdown file into your app as a Prop derived via getInitialProps! This means that the markdown content will be passed down from the server on initial page load.  

```typescript
export default class ToutorialPage extends Component<Props, State> {
  static async getInitialProps(ctx) {
    const name = ctx.query.name;
    const markdown = await require(`./../../tutorials/${name}.md`);
    return {
      markdown: markdown.default,
      name
    };
  }
render () {
   return (
      <ReactMarkdown
        source={this.props.markdown}
        escapeHtml={false}
        renderers={{}}
      />
    ) 
  }
}
```

Hooking into Rendering to modify State  
In the example above you can see that react-markdown lets us provide special renderers for each HTML element. 2 things that were important to this project were rendering code properly, and adding sub-navigation to each page.  
Adding code was easy, as we already had a component for rendering code based on react-syntax-highlighter.  

```typescript
import { Component } from "react";
import SyntaxHighlighter from "react-syntax-highlighter";
import { docco } from "react-syntax-highlighter/dist/cjs/styles/hljs";
interface Props {
  language?: string;
  showLineNumbers?: boolean;
  value?: string;
}
export default class extends Component<Props> {
  render() {
    const language = this.props.language || "typescript";
    const showLineNumbers = this.props.showLineNumbers || false;
return (
      <SyntaxHighlighter
        language={language}
        style={docco}
        showLineNumbers={showLineNumbers}
      >
        {this.props.value ? this.props.value : this.props.children}
      </SyntaxHighlighter>
    );
  }
}
We just pass that component into our example above:
import Code from "./../../components/code";
export default class ToutorialPage extends Component<Props, State> {
  static async getInitialProps(ctx) {
    const name = ctx.query.name;
    const markdown = await require(`./../../tutorials/${name}.md`);
    return {
      markdown: markdown.default,
      name
    };
  }
render () {
   return (
      <ReactMarkdown
        source={this.props.markdown}
        escapeHtml={false}
        renderers={{
          code: Code // <-- HERE
        }}
      />
    ) 
  }
}
```

Adding navigation was a bit tricker. We accomplished this by creating a custom renderer for Headers that also built up a list of all the section headers into the page’s state with this new parseHeading method:

```typescript
parseHeading({ children }) {
    const { sectionHeadings } = this.state;
return (
      <div>
        {children.map(child => {
          const stringValue = child.props.value;
          if (sectionHeadings.indexOf(stringValue) < 0) {
            sectionHeadings.push(stringValue); // <-- Build our list of headings
            this.setState({ sectionHeadings });
          }
const style = Theme.typeography.h2;
return (
              <div>
                <br />
                <h2 id={stringValue} style={style}>
                  <span style={{ fontWeight: 300, fontSize: 36 }}>{child}</span>
                </h2>
                <RedLine />
              </div>
          );
        })}
      </div>
    );
  }
  ```

this.state.sectionHeadings is built in our render as we parse the headers. We then have this available to the rest of the page to draw our side navigation!  
Notes:  
Since we are changing state within the render method, it’s easy to get into an infinite loop. That’s why we need to only modify the list of headers (sectionHeadings) if the header isn’t present.
Since we have access to the header’s render method now, we add more style! Here we are adding our custom RedLine component to draw a line under the header of each section

[完整代码](https://github.com/actionhero/www.actionherojs.com/blob/master/pages/tutorials/%5Bname%5D.tsx)
```typescript
import ReactMarkdown from "react-markdown/with-html";
import { Component } from "react";
import { Row, Col } from "react-bootstrap";
import { Waypoint } from "react-waypoint";
import DocsPage from "../../components/layouts/docsPage";
import Code from "./../../components/code";
import Link from "next/link";
import Theme from "./../../components/theme";
import RedLine from "./../../components/elements/redLine";
import BigButton from "./../../components/buttons/bigButton";

interface Props {
  markdown: string;
  name: string;
}

interface State {
  sectionHeadings: Array<any>;
  currentlyVisableSections: {};
  contentHeight: number;
}

export default class TutorialPage extends Component<Props, State> {
  static async getInitialProps(ctx) {
    const name = ctx.query.name;
    const markdown = await require(`./../../tutorials/${name}.md`);
    return {
      markdown: markdown.default,
      name,
    };
  }

  constructor(props) {
    super(props);
    this.state = {
      currentlyVisableSections: {},
      sectionHeadings: [],
      contentHeight: 0,
    };
  }

  componentDidMount() {
    this.measureContentHeight();
  }

  measureContentHeight() {
    const element = document.getElementById("tutorialPageContent");
    if (element) {
      const height = element.offsetHeight;
      this.setState({ contentHeight: height });
    }
  }

  waypointEnterCallback(id, { previousPosition }) {
    this.state.currentlyVisableSections[id] = true;
    this.highlightSideNav();
  }

  waypointExitCallback(id) {
    this.state.currentlyVisableSections[id] = false;
    this.highlightSideNav();
  }

  highlightSideNav() {
    Object.keys(this.state.currentlyVisableSections).forEach((section) => {
      const value = this.state.currentlyVisableSections[section];
      const element = document.getElementById(`sidenav-${section}`);

      if (value) {
        element.style.color = Theme.colors.red;
        element.style.fontWeight = "400";
      } else {
        element.style.color = Theme.typography.h2.color;
        element.style.fontWeight = Theme.typography.h2.fontWeight.toString();
      }
    });
  }

  parseHeading({ children }) {
    const { sectionHeadings } = this.state;

    return (
      <div>
        {children.map((child) => {
          const stringValue = child.props.value;
          if (sectionHeadings.indexOf(stringValue) < 0) {
            sectionHeadings.push(stringValue);
            this.setState({ sectionHeadings });
          }

          const style = Theme.typography.h2;

          return (
            <Waypoint
              key={child.key}
              onEnter={(args) => {
                this.waypointEnterCallback(stringValue, args);
              }}
              onLeave={(args) => {
                this.waypointExitCallback(stringValue);
              }}
            >
              <div id={stringValue}>
                <br />
                <h2 style={style}>
                  <span style={{ fontWeight: 300, fontSize: 36 }}>{child}</span>
                </h2>
                <RedLine />
              </div>
            </Waypoint>
          );
        })}
      </div>
    );
  }

  render() {
    const { name } = this.props;
    const { sectionHeadings, contentHeight } = this.state;

    const aStyle = {
      fontWeight: 300,
      fontSize: 18,
      lineHeight: "1.6em",
      color: null,
    };

    return (
      <DocsPage
        showSolutions
        titleSection={{
          title: name.charAt(0).toUpperCase() + name.slice(1),
          icon: `/static/images/${Theme.icons[name]}`,
        }}
      >
        <Row id="tutorialPageContent">
          <Col md={9}>
            <ReactMarkdown
              source={this.props.markdown}
              escapeHtml={false}
              renderers={{
                code: Code,
                heading: (node) => {
                  return this.parseHeading(node);
                },
              }}
            />
          </Col>
          <Col md={3} className="d-none d-md-block">
            <div style={{ height: contentHeight }}>
              <div style={{ paddingTop: 90, position: "sticky", top: 0 }}>
                <ul
                  style={{
                    listStyleType: "none",
                    paddingLeft: 0,
                    marginLeft: 0,
                  }}
                >
                  {sectionHeadings.map((section) => {
                    return (
                      <li key={`section-${section}`}>
                        <a
                          href={`#${section}`}
                          className="text-danger"
                          style={aStyle}
                          id={`sidenav-${section}`}
                        >
                          {section}
                        </a>
                      </li>
                    );
                  })}
                </ul>
              </div>
            </div>
          </Col>
        </Row>

        <br />

        <BigButton
          href="/tutorials"
          backgroundColor={Theme.colors.red}
          textColor={Theme.colors.white}
        >
          Back to Tutorials
        </BigButton>
      </DocsPage>
    );
  }
}
```
