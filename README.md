# プロジェクト名

COACHTECH お問い合わせフォーム

# 概要

ユーザーが必須項目を入力し、登録されているタグ、お問い合わせの種類を選択して送信ができる。
登録されたメールアドレスとパスワードでログインした管理者のみ、お問い合わせ一覧・詳細の閲覧、タグの編集ができる。



## ER図

```mermaid
erDiagram
    categories ||--o{ contacts : "１つのカテゴリーは複数の問い合わせを持つ”
    contacts ||--o{ contact_tag : "1つの問い合わせは複数のタグを持つ"
    tags ||--o{ contact_tag : "1つのタグは複数の問い合わせを持つ"


    users {
        bigint id PK
        string name "管理者名"  
        string email "管理者メールアドレス"
        timestamp email_verified_at
        string password "管理者パスワード"
        boolean remember_token 
        timestamp created_at
        timestamp updated_at
    }

    categories {
        bigint id PK
        string content "カテゴリーの種類"
        timestamp created_at
        timestamp updated_at
    }

    contacts {
        bigint id PK
        bigint category_id FK
        string first_name 
        string last_name
        tinyint gender
        string email
        string tel
        string address
        string building
        string detail "お問い合わせ内容"
        timestamp created_at
        timestamp updated_at
    }

    tags {
        bigint id PK
        string name
        timestamp created_at
        timestamp updated_at
    }

    contact_tag {
        bigint id PK
        bigint contact_id FK
        bigint tag_id FK
        timestamp created_at
        timestamp updated_at
    }

## 環境構築手順

1. **リポジトリをクローン**

    ````bash
    git clone https://git@github.com:haruna-kojima/contact-form.git
    ````

2. **.envファイルの準備**

    ````bash
    cp .env.example .env
    ````

3. **Composer依存パッケージのインストール**

    ````bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
    ````

4. **Laravel Sailの起動**

    ````bash
    ./vendor/bin/sail up -d
    ````

5. **アプリケーションキーの生成**

    ````bash
    ./vendor/bin/sail artisan key:generate
    ````

6. **データベースのマイグレーションと初期データ投入**

    ````bash
    ./vendor/bin/sail artisan migrate --seed
    ````

7. **フロントエンドのビルド**

    ````bash
    ./vendor/bin/sail npm install
    ./vendor/bin/sail npm run dev
    ````

8. **アプリケーションへのアクセス**

    ````bash
    お問い合わせフォーム http://localhost
    管理者ログイン http://localhost/login
    ````

## 使用技術

````bash
- Docker version 29.5.3
- Laravel 10.x
- PHP 8.2
- Tailwind CSS
- DB MySQL8.0
- Webサーバー Nginx
- フロントエンド Vite, Tailwind CSS ^3.4.0
- 開発ツール Docker,Laravel Sail,phpAcmin
````

## APIエンドポイント一覧
    
    ````bash
| 処理内容 | HTTPメソッド | パス (URL) | 必須パラメータ | HTTPステータス |
| :--- | :---: | :--- | :--- | :---: |
| **お問い合わせ一覧取得** | `GET` | `/api/v1/contacts` | 任意 (検索・ページネーション) | `200 OK` |
| **お問い合わせ詳細取得** | `GET` | `/api/v1/contacts/{id}` | パスパラメータ: `id` | `200 OK` / `404` |
| **お問い合わせ新規作成** | `POST` | `/api/v1/contacts` | リクエストボディ (全必須項目) | `201 Created` / `422` |
| **お問い合わせ情報の更新** | `PUT` | `/api/v1/contacts/{id}` | リクエストボディ、パス: `id` | `200 OK` / `404` / `422` |
| **お問い合わせデータの削除** | `DELETE` | `/api/v1/contacts/{id}` | パスパラメータ: `id` | `204 No Content` / `404` |
````

## テスト実行
    ````bash
    単体テストの実行
    .vendor/bin/sail artisan test tests/Unit/ContactWebUnitTest.php
    すべてのテストを一括実行
    ./vendor/bin/sail artisan test --display-deprecations
    ````

## 開発環境URL

    ````bash
    お問い合わせフォーム http://localhost
    管理者ログイン http://localhost/login
    ````

## 作成者

小島　春菜
