设置
git config --global http.proxy http://127.0.0.1:1087
git config --global https.proxy http://127.0.0.1:1087

git config --global http.proxy 'socks5://127.0.0.1:1080' 
git config --global https.proxy 'socks5://127.0.0.1:1080'



取消
git config --global --unset http.proxy
git config --global --unset https.proxy

export http_proxy=http://127.0.0.1:1087;export https_proxy=http://127.0.0.1:1087;