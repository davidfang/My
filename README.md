# MySKD

## Before installation

* Read about [docker](https://www.docker.com)
* Install it
* Read about [composer](http://community.dev.croy.cn)
* Install it
* 更新全局composer`composer global require "fxp/composer-asset-plugin"`
* 更新composer `composer update `

## Installation

1. 复制vhost.conf.docker.dist 到vhost.conf在根目录下面，并且修改配置服务域名
2. 修改docker-compose.yml中数据库名，用户名，密码等信息
```javascript
db:
  image: mysql:5.6
  volumes:
    - /var/lib/mysql
  ports:
    - "127.0.0.1:33060:3306"
  environment:
    MYSQL_ROOT_PASSWORD: root
    MYSQL_DATABASE: MySKD
    MYSQL_USER: myskd_dbu
    MYSQL_PASSWORD: myskd_pass
```
3. vim common/config/db.php 修改相关数据库链接信息如下(和上一步中数据库设置一致)：
```php
return [
    'db'=>[
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=db;dbname=MySKD',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
        'tablePrefix'=>'xw_'
    ],
];
```
4. Run docker-compose build
5. Run docker-compose up -d
6. 安装项目 docker-compose run cli migrate
7. 修改你的HOST绑定，对应服务域名和IP，这样你就可以访问项目了
  [http://api.myskd.dev](http://api.myskd.dev) 
  [http://backend.myskd.dev](http://backend.myskd.dev)
  [http://www.myskd.dev](http://www.myskd.dev)