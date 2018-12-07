# dxf配置

## 数据库配置
### bridge-cfg-bridge.cfg
DB ip='192.168.200.131'
DB id='game'
DB pw='uu5!^%jg'
DB name='d_channel'


192.168.0.104


docker run -d --name tidb \
  -p 4000:4000 \
  pingcap/tidb:latest 


/Users/wenj91/docker/mysql

#修改对象（文件）的安全上下文。比如：用户：角色：类型：安全级别
  chcon -Rt svirt_sandbox_file_t /Users/wenj91/docker/mysql

docker run -p 4001:4001 -v /Users/wenj91/docker/mysql:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=hello123 --name mariadb -d --restart unless-stopped docker.io/mariadb:latest 


docker run -p 5000:3306 -e MYSQL_ROOT_PASSWORD=root  --name mariadb -d mariadb


## 全局替换
sed -i "s/192.168.200.131/192.168.0.104/g" `find . -type f -name "*.tbl"`
sed -i "s/192.168.200.131/192.168.0.104/g" `find . -type f -name "*.cfg"`


sed -i "s/192.168.0.104/192.168.200.131/g" `find . -type f -name "*.tbl"`
sed -i "s/192.168.0.104/192.168.200.131/g" `find . -type f -name "*.cfg"`


sed -i "s/10.211.55.5/192.168.200.131/g" `find . -type f -name "*.tbl"`
sed -i "s/10.211.55.5/192.168.200.131/g" `find . -type f -name "*.cfg"`



sed -i "s/192.168.200.131/192.168.0.104/g"