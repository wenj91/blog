# [Install Elasticsearch on macOS with Homebrew](https://www.elastic.co/guide/en/elasticsearch/reference/7.8/brew.html)

Install Elasticsearch on macOS with Homebrewedit
Elastic publishes Homebrew formulae so you can install Elasticsearch with the Homebrew package manager.

To install with Homebrew, you first need to tap the Elastic Homebrew repository:

brew tap elastic/tap
Once you’ve tapped the Elastic Homebrew repo, you can use brew install to install the default distribution of Elasticsearch:

brew install elastic/tap/elasticsearch-full
This installs the most recently released default distribution of Elasticsearch. To install the OSS distribution, specify elastic/tap/elasticsearch-oss.

Directory layout for Homebrew installsedit
When you install Elasticsearch with brew install the config files, logs, and data directory are stored in the following locations.

Type	Description	Default Location	Setting
home

Elasticsearch home directory or $ES_HOME

/usr/local/var/homebrew/linked/elasticsearch-full

bin

Binary scripts including elasticsearch to start a node and elasticsearch-plugin to install plugins

/usr/local/var/homebrew/linked/elasticsearch-full/bin

conf

Configuration files including elasticsearch.yml

/usr/local/etc/elasticsearch

ES_PATH_CONF

data

The location of the data files of each index / shard allocated on the node. Can hold multiple locations.

/usr/local/var/lib/elasticsearch

path.data

logs

Log files location.

/usr/local/var/log/elasticsearch

path.logs

plugins

Plugin files location. Each plugin will be contained in a subdirectory.

/usr/local/var/homebrew/linked/elasticsearch/plugins

Next stepsedit
You now have a test Elasticsearch environment set up. Before you start serious development or go into production with Elasticsearch, you must do some additional setup:

Learn how to configure Elasticsearch.
Configure important Elasticsearch settings.
Configure important system settings.