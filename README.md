# フリマアプリ

企業開発の独自フリマアプリ
アイテムの出品・購入を行える

## 環境構築

**Docker ビルド**

1. アプリケーションをクローンするディレクトリに移動
2. `git clone git@github.com:Koharu5810/flea_market.git`
3. `cd flea_market`
4. DockerDesktop アプリを立ち上げる または `open -a docker`
5. `docker-compose up -d --build`


```bash
services:
  mysql:
    platform: linux/amd64

  phpmyadmin:
    platform: linux/amd64
```

**Laravel 環境構築**

1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.env ファイルを作成
4. .env に以下の環境変数を追加

```text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

5. アプリケーションキーの作成

```bash
php artisan key:generate
```

6. マイグレーションの実行

```bash
php artisan migrate
```

7. シーディングの実行

```bash
php artisan db:seed
```

8. シンボリックリンクの作成
``` bash
php artisan storage:link
```

## 使用技術(実行環境)

| 言語・フレームワーク | バージョン |
| :------------------- | :--------- |
| PHP                  | 8.3.13     |
| Laravel              | 8.83.27    |
| MySQL                | 9.0.1      |

## ER 図

![alt](erd.png)

## URL

- 開発環境 : http://localhost/products
- phpMyAdmin : http://localhost:8080/
