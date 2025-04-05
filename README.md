# coachtechフリマ


## 環境構築
### Docker ビルド
1. git clone git@github.com:misato-kataoka/coachtechFurima.git

2. cd coachtechFurima

3. docker-compose up -d --build

### Laravelの環境構築
1. docker-compose exec php bash

2. composer install

3. .env.exmpleファイルから.envファイルを作成し、環境変数を以下の通りに変更
```
  DB_CONNECTION=mysql
  DB_HOST=mysql
  DB_PORT=3306
  DB_DATABASE=larabel_db
  DB_USERNAME=larabel_user
  DB_PASSWORD=laravel_pass
```
4. docker-compose exec php bash

5. アプリケーションキーの作成
```
　php artisan key:generate
```
6. マイグレーションの実行
```
  php artisan migrate
```
7. シーディングを実行する
```
  php artisan db:seed
```

## ER図


## 使用技術

-php 7.4.9

-Laravel (v8.6.12)

-MySQL 8.0.26

## URL

-開発環境 http://localhost/

-phpMyAdmin http://localhost:8080
