# coachtechフリマ


## 環境構築
### Docker ビルド
1. **リポジトリをクローン**
   ```bash
   git clone git@github.com:misato-kataoka/coachtechFurima.git

2. cd coachtechFurima

3. docker compose up -d --build

### Laravelの環境構築
1. docker compose exec php bash

2. composer install

3. .env.exmpleファイルから.envファイルを作成し、環境変数を以下の通りに変更
```
  DB_CONNECTION=mysql
  DB_HOST=mysql
  DB_PORT=3306
  DB_DATABASE=laravel_db
  DB_USERNAME=laravel_user
  DB_PASSWORD=laravel_pass

STRIPE_KEY=pk_test_51RRVovH1ap6ABKzgQAn4S9wxQIN0slhHLU6AmiciLYL1NG0iR0vRSuk3iSDOxMJtMUYC7jzahEJXpQgCHrsGKZia00awOr9IHV
STRIPE_SECRET=sk_test_51RRVovH1ap6ABKzg5R9B8ykI65tfvTttnAGKPKddAWvG606XMgkbXmRNJOHWkOzt0zkOs5RAUIOyzPPN0Zf6c9aN00YKUK6lCC
```
4. docker compose exec php bash

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
### メール認証
  mailtrapを使用しています。  
  以下のリンクから会員登録をしてください。  　
  https://mailtrap.io/  
  
  メールボックスのIntegrationsから 「laravel 7.x and 8.x」を選択し、  　
  .envファイルのMAIL_MAILERからMAIL_ENCRYPTIONまでの項目をコピー＆ペーストしてください。  　
  MAIL_FROM_ADDRESSは任意のメールアドレスを入力してください。

## ER図
![Image](https://github.com/user-attachments/assets/b6f3634f-dc57-45c7-9baf-06ba36d2703f)

## テストアカウント
* **username:** '鈴木　一郎'
* **Email:** 'testuser@example.com'
* **password:** 'password123'
---  
* **username:** '山田　花子'
* **Email:** 'sampleuser@example.com'
* **password:** 'password456'
---  
* **username:** '田中　次郎'
* **Email:** 'tanakajirou@example.com'
* **password:** 'password789'

## 使用技術

-php 7.4.9

-Laravel (v8.6.12)

-MySQL 8.0.26

-Docker

-Stripe API

## URL

-開発環境 http://localhost/

-phpMyAdmin http://localhost:8080
