192.168.200.131
192.168.0.104


sed -i "s/192.168.0.101/192.168.0.104/g" `find . -type f -name "*.tbl"`
sed -i "s/192.168.0.101/192.168.0.104/g" `find . -type f -name "*.cfg"`


sed -i "s/192.168.0.104/192.168.0.101/g" `find . -type f -name "*.tbl"`
sed -i "s/192.168.0.104/192.168.0.101/g" `find . -type f -name "*.cfg"`

sed -i "s/docker.for.mac.localhost/192.168.65.2/g" `find . -type f -name "*.tbl"`
sed -i "s/docker.for.mac.localhost/192.168.65.2/g" `find . -type f -name "*.cfg"`

docker.for.mac.localhost

## 游戏登录器实现

```go
	data := fmt.Sprintf("%08x010101010101010101010101010101010101010101010101010101010101010155914510010403030101", "18000600") // 18000600 uid
	
	// rsa 私钥加密
	en, _ := gorsa.PriKeyEncrypt(data, privateKey) 
	fmt.Println(en)

	// base64编码
	fmt.Println(string(base64.StdEncoding.EncodeToString([]byte(en))))

	// 最后实现: dnf.exe base64编码后内容
```