# [Multi-“match-phrase” query in Elastic Search](https://stackoverflow.com/questions/30020178/multi-match-phrase-query-in-elastic-search)

It turns out you can do this by enabling phrase semantics for multi_match.

To do this you add a type: attribute to the multi_match syntax as below:

GET /_search
{
  "query": {
    "multi_match" : {
      "query":      "quick brown fox",
      "type":       "phrase",
      "fields":     [ "subject", "message" ]
    }
  }
}
Once you think of it that way (vs. enabling "multi" support on other search clauses) it fits in where you'd expect.

[ref](https://www.elastic.co/guide/en/elasticsearch/reference/6.5/query-dsl-multi-match-query.html#type-phrase)