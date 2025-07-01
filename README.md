# フリマアプリ

企業開発の独自フリマアプリ  
アイテムの出品・購入を行える  
メール認証によって、認証済みユーザのみが取引などの操作を行える

## 環境構築

**Docker ビルド**

1. アプリケーションをクローンするディレクトリに移動
2. `git clone git@github.com:Koharu5810/flea_market.git`
3. `cd flea_market`
4. DockerDesktop アプリを立ち上げる（`open -a docker`）
5. `docker-compose up -d --build`

**Laravel 環境構築**

1. `docker-compose exec php bash`
2. `composer install`
3. .env ファイルを作成（cp .env.example .env）し、以下の環境変数を修正する

```text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_ENCODING=UTF-8
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=test
MAIL_PASSWORD=pass
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=test@abc.com
MAIL_FROM_NAME="${flea_market}"
```

4. アプリケーションキーを作成し、キャッシュをクリア

```bash
php artisan key:generate

php artisan config:clear
php artisan cache:clear
```

5. Stripe連携の設定をする

https://stripe.com/jp にアクセスし、アカウントを用意の上サインインする
Stripeダッシュボードの画面左にある「開発者>APIキー」へアクセスし、以下をコピーし.envに記述する
　・公開可能キー：pk_test_から始まるトークン
　・シークレットキー：sk_test_から始まるトークン

```text
STRIPE_KEY=pk_test_あなたの公開可能キー
STRIPE_SECRET=sk_test_あなたの公開可能キー
```

6. Stripeパッケージのインストール、必要に応じて動作確認

``` bash
composer require stripe/stripe-php

php artisan serve
```

6. マイグレーション・シーディングの実行、シンボリックリンクの作成

```bash
php artisan migrate --seed
php artisan storage:link
```
<br>

**テスト用データベースの設定**

1. MySQLコンテナ内でコマンド実行

``` bash
mysql -u root -p
CREATE DATABASE test_database;
```

2. テスト用の環境変数ファイルを作成し、テスト用データベース設定に編集する

``` bash
cp .env.example .env.testing


APP_ENV=test

DB_CONNECTION=mysql_test
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=test_database
DB_USERNAME=root
DB_PASSWORD=root
```

3. テスト用のアプリケーションキーを作成し、キャッシュクリアする

``` bash
php artisan key:generate --env=testing

php artisan config:clear
php artisan cache:clear
```

4. テスト用データベースにマイグレーションとシーディングを適用する

``` bash
php artisan migrate --env=testing
php artisan db:seed --env=testing
```


## ログイン情報

**一般ユーザ　会員登録後のメール認証**

アプリケーションをブラウザで確認時に、会員登録画面で登録後メール認証を行うには  
http://localhost:8025  
へダイレクトし、本文記載の認証ボタンをクリックする。
<br><br>

**一般ユーザのログイン**

http://localhost/login へアクセス
UsersTableSeederに記述のメールアドレス、パスワードを使用

**商品購入処理実行時のStripe操作**

アプリケーションをブラウザで確認時に、商品購入画面から遷移したStripeでの購入処理実行には  
カード支払いを選択し、カード番号に「4242 4242 4242 4242」を入力する。  
（その他の入力情報は適当なデータでOK）
<br><br>


## 使用技術(実行環境)

| 言語・フレームワーク | バージョン |
| :------------------- | :--------- |
| PHP                  | 8.3.13     |
| Laravel              | 8.83.27    |
| MySQL                | 9.0.1      |
| Stripe               | 9.9.0      |
| MailHog              |            |

## ER 図

![alt](erd.png)

## URL

- 開発環境 : http://localhost/
- phpMyAdmin : http://localhost:8080/
- MailHog : http://localhost:8025/
