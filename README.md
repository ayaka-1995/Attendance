# Attendance

## 環境構築

本プロジェクトはLaravel Sail(Docker)で構築します。ローカルにPHP/Composerがなくても構築できる

# 1.リポジトリのクローン
```
git clone git@github.com:ayaka-1995/Attendance.git
cd Attendance
```

# 2. 環境変数ファイルの作成
```
cp .env.example .env
```

# 3.Composer依存のインストール（Docker経由）
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html\
    laravelsail/php82-composer:latest \
    composer install
```

# 4.Sailの起動
```
./vendor/bin/sail up -d
```

# 5.アプリケーションキーの生成
```
sail artisan key:generate
```

# 6.マイグレーション&シーディング
```
sail artisan migrate --seed
```

# 7.フロントエンドアセットのビルド
```
sail npm install
sail npm run build #開発中にホットリロードする場合は sail npm run dev
```

# 8.アクセス
・アプリ：http://localhost

### テスト実行
```
sail artisan test
```


## テーブル仕様

### users テーブル

| カラム名          | 型           | primary key | unique key | not null | foreign key |
| ----------------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id                | bigint       | ◯           |            | ◯        |             |
| name              | varchar(255) |             |            | ◯        |             |
| email             | varchar(255) |             | ◯          | ◯        |             |
| email_verified_at | timestamp    |             |            |          |             |
| password          | varchar(255) |             |            | ◯        |             |
| remember_token    | varchar(100) |             |            |          |             |
| created_at        | timestamp    |             |            |          |             |
| updated_at        | timestamp    |             |            |          |             |
| admin_status      | tinyint      |             |            | ◯        |             |
| attendance_status | varchar(255) |             |            | ◯        |             |

### attendance_records テーブル

| カラム名         | 型           | primary key | unique key | not null | foreign key |
| ---------------- | ------------ | ----------- | ---------- | -------- | ----------- |
| id               | bigint       | ◯           |            | ◯        |             |
| user_id          | bigint       |             |            | ◯        | users(id)   |
| date             | date         |             |            | ◯        |             |
| clock_in         | time         |             |            | ◯        |             |
| clock_out        | time         |             |            |          |             |
| total_time       | varchar(255) |             |            |          |             |
| total_break_time | varchar(255) |             |            |          |             |
| comment          | varchar(255) |             |            |          |             |
| created_at       | timestamp    |             |            |          |             |
| updated_at       | timestamp    |             |            |          |             |

### breaks テーブル

| カラム名             | 型        | primary key | unique key | not null | foreign key            |
| -------------------- | --------- | ----------- | ---------- | -------- | ---------------------- |
| id                   | bigint    | ◯           |            | ◯        |                        |
| attendance_record_id | bigint    |             |            | ◯        | attendance_records(id) |
| break_in             | time      |             |            | ◯        |                        |
| break_out            | time      |             |            |          |                        |
| created_at           | timestamp |             |            |          |                        |
| updated_at           | timestamp |             |            |          |                        |

### applications テーブル

| カラム名             | 型           | primary key | unique key | not null | foreign key            |
| -------------------- | ------------ | ----------- | ---------- | -------- | ---------------------- |
| id                   | bigint       | ◯           |            | ◯        |                        |
| user_id              | bigint       |             |            | ◯        | users(id)              |
| attendance_record_id | bigint       |             |            | ◯        | attendance_records(id) |
| approval_status      | varchar(255) |             |            | ◯        |                        |
| application_date     | date         |             |            | ◯        |                        |
| new_date             | date         |             |            | ◯        |                        |
| new_clock_in         | time         |             |            | ◯        |                        |
| new_clock_out        | time         |             |            | ◯        |                        |
| comment              | varchar(255) |             |            | ◯        |                        |
| created_at           | timestamp    |             |            |          |                        |
| updated_at           | timestamp    |             |            |          |                        |

### application_breaks テーブル

| カラム名       | 型        | primary key | unique key | not null | foreign key      |
| -------------- | --------- | ----------- | ---------- | -------- | ---------------- |
| id             | bigint    | ◯           |            | ◯        |                  |
| application_id | bigint    |             |            | ◯        | applications(id) |
| break_in       | time      |             |            | ◯        |                  |
| break_out      | time      |             |            |          |                  |
| created_at     | timestamp |             |            |          |                  |
| updated_at     | timestamp |             |            |          |                  |

## ER 図
![alt](ER.png)


## API エンドポイント一覧（応用）

公開 API v1。読み取り系（GET）は認証不要、書き込み系（POST / PUT / DELETE）は Sanctum 認証必須で、PUT / DELETE は本人または管理者のみ操作可能。

| HTTPメソッド | URI | 説明 | 認証・認可 |
| ------------ | --------------------------------------------- | ------------------ | ----------------------------------------- |
| GET | /api/v1/attendance-records | 勤怠一覧を取得する | 不要 |
| GET | /api/v1/attendance-records/{attendanceRecord} | 勤怠詳細を取得する | 不要 |
| POST | /api/v1/attendance-records | 勤怠を新規登録する | Sanctum 必須 |
| PUT / PATCH | /api/v1/attendance-records/{attendanceRecord} | 勤怠を更新する | Sanctum + Policy（本人または管理者のみ） |
| DELETE | /api/v1/attendance-records/{attendanceRecord} | 勤怠を削除する | Sanctum + Policy（本人または管理者のみ） |

### ログイン情報
一般ユーザー
    id: user1@example.com/user2@example.com
    pass:password
管理者
    id: user3@example.com
    pass:password

### URL

・開発環境:http://localhost/
・phpMyAdmin:http://localhost:8080/
・Mailpit:http://localhost: