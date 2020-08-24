# [no active connection found: no Elasticsearch node available]()

```go
client, err := elastic.NewClient(
    elastic.SetSniff(false), // 解决方法，将sniff设置为false
    elastic.SetURL(conf.ElsURLs...),
    elastic.SetBasicAuth(conf.ElsUser, conf.ElsPassword),
)
```