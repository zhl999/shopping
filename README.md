# shopping
购物网站后台
## 说明
1. 基于thinkphp5.1.37
2. git没有提交的文件有/vender、/thinkphp、/config/database.php,需要手动上传
3. 如果是nginx 参考5.1手册，如果nginx配置低，则把nginxde的url进行重写，apache随意
```
location /shop/public/ {
  if (!-e $request_filename){
    rewrite  ^/shop/public/(.*)$  /shop/public/index.php?s=/$1  last;
  }
}
```
