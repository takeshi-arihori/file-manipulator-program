# File Manipulator Program

<img width="1363" alt="Screenshot 2025-05-27 at 10 15 28 AM" src="https://github.com/user-attachments/assets/5a2f1793-d2f5-4707-a7ae-1b210de87dca" />
<img width="1356" alt="Screenshot 2025-05-27 at 10 15 37 AM" src="https://github.com/user-attachments/assets/76ff3875-8904-4018-998e-387b58f92f54" />


https://github.com/user-attachments/assets/3efe784e-6801-42a2-babd-9c621549d650



## プロジェクト概要

Laravel 12を使用したファイル操作Webアプリケーションです。ファイルのアップロード、各種操作（反転、コピー、複製、文字列置換）、結果のダウンロード、および操作ログの管理機能を提供しています。

## 主な機能

### 1. ファイル操作機能

- **ファイル内容の反転**: アップロードしたファイルの内容を逆順にします
- **ファイルのコピー**: ファイル内容を2回連結した新しいファイルを作成します
- **ファイル内容の複製**: 指定回数だけファイル内容を繰り返します
- **文字列置換**: 指定した文字列を別の文字列に置換します

### 2. ログ管理機能

- **操作ログの記録**: 各ファイル操作の詳細ログを自動記録
- **ログの表示**: 操作タイプ別、日付別でのログ表示
- **開閉式アコーディオン**: ログ一覧の見やすい表示
- **フィルター機能**: 操作タイプや日付でのログ絞り込み

### 3. ファイル管理

- **自動ダウンロード**: 処理完了後の結果ファイル自動ダウンロード
- **操作別保存**: `storage/app/public/` 内の操作別ディレクトリに整理保存
  - `file-reverse/` - ファイル内容の反転
  - `file-copy/` - ファイルのコピー
  - `file-duplicate/` - ファイル内容の複製
  - `file-replace/` - 文字列置換

## 技術スタック

- **Backend**: Laravel 12 (PHP 8.3)
- **Database**: MySQL 8.0
- **Frontend**: TailwindCSS, Alpine.js
- **Infrastructure**: Docker, Docker Compose
- **Database Management**: phpMyAdmin

## 環境要件

- PHP 8.3以上
- Composer
- Node.js & npm
- Docker & Docker Compose（推奨）

## セットアップ方法

### Docker を使用する場合（推奨）

#### 1. リポジトリをクローン

```bash
git clone [リポジトリURL]
cd file-manipulator-program
```

#### 2. Docker コンテナを起動

```bash
docker compose up -d
```

#### 3. アプリケーションにアクセス

```
http://localhost:8081
```

### ローカル環境での実行

#### 1. 依存関係のインストール

```bash
cd src
docker compose exec php composer install
docker compose exec php npm install
```

#### 2. 環境設定

```bash
docker compose exec php cp .env.example .env
docker compose exec php php artisan key:generate
```

#### 3. アセットのビルド

```bash
docker compose exec php npm run build
```

#### 4. 開発環境起動(npm run dev)

```bash
docker compose exec php npm run dev
```

## ディレクトリ構造

```zsh
.
├── README.md
├── compose.yaml                 # Docker Compose設定
├── docker/                      # Docker関連ファイル
├── devlog/                      # 開発ログ・ドキュメント
└── src/                         # Laravelアプリケーション
    ├── app/
    │   ├── Http/Controllers/
    │   │   ├── FileManipulatorController.php
    │   │   └── LogController.php
    │   ├── Services/
    │   │   └── FileManipulatorService.php
    │   └── Console/Commands/
    ├── resources/
    │   ├── views/
    │   │   ├── file-manipulator/
    │   │   ├── logs/
    │   │   └── layouts/
    │   └── css/
    ├── routes/
    │   └── web.php
    ├── storage/
    │   ├── app/public/            # 処理結果ファイル保存先
    │   └── logs/                  # アプリケーションログ
    ├── tests/
    │   └── Feature/
    └── config/
```

## 使用方法

### ファイル操作

1. トップページ（`http://localhost:8081`）にアクセス
2. 実行したい操作のカードを選択
3. ファイルをアップロード
4. 必要に応じてパラメータを入力（複製回数、置換文字列など）
5. 「実行してダウンロード」ボタンをクリック
6. 処理完了後、結果ファイルが自動ダウンロード

### ログ確認

1. ナビゲーションの「📊 ログ」をクリック
2. 操作タイプや日付でフィルター
3. アコーディオンをクリックして詳細ログを表示
4. 「すべて開く/閉じる」ボタンで一括制御

## テスト

```bash
cd src
php artisan test
```

## 開発

### アセットの監視
```bash
cd src
npm run dev
```

### コードスタイル
```bash
cd src
./vendor/bin/pint
```

## Docker構成

### サービス一覧

- **php**: Laravel アプリケーション (ポート: 8081)
- **mysql**: MySQL 8.0 データベース (ポート: 33063)
- **phpmyadmin**: データベース管理ツール (ポート: 8080)

### 起動方法

```bash
# コンテナの起動
docker compose up -d

# コンテナの停止
docker compose down
```

### アクセス先

- **Webアプリケーション**: http://localhost:8081
- **phpMyAdmin**: http://localhost:8080
- **MySQL**: localhost:33063

## データベース設計

### operation_logs テーブル
操作ログの基本情報を保存

| カラム名 | 型 | 説明 |
|---------|-----|------|
| id | bigint | 主キー |
| operation_type | varchar | 操作タイプ (reverse, copy, duplicate, replace) |
| input_filename | varchar | 入力ファイル名 |
| output_filename | varchar | 出力ファイル名 |
| operation_details | text | 操作詳細 (JSON) |
| execution_time | decimal | 実行時間（秒） |
| status | enum | ステータス (success, error) |
| error_message | text | エラーメッセージ |
| file_path | varchar | 保存ファイルパス |
| file_size | int | ファイルサイズ（バイト） |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

### file_operations テーブル
ファイル操作の詳細情報を保存

| カラム名 | 型 | 説明 |
|---------|-----|------|
| id | bigint | 主キー |
| operation_log_id | bigint | 操作ログID (外部キー) |
| original_filename | varchar | 元ファイル名 |
| stored_filename | varchar | 保存ファイル名 |
| file_path | varchar | ファイルパス |
| operation_directory | varchar | 操作ディレクトリ |
| file_size | int | ファイルサイズ |
| mime_type | varchar | MIMEタイプ |
| file_content_preview | text | ファイル内容プレビュー |
| is_downloaded | boolean | ダウンロード済みフラグ |
| downloaded_at | timestamp | ダウンロード日時 |
| created_at | timestamp | 作成日時 |
| updated_at | timestamp | 更新日時 |

## 開発環境セットアップ

### 1. リポジトリのクローン
```bash
git clone <repository-url>
cd file-manipulator-program
```

### 2. Docker環境の起動
```bash
docker compose up -d
```

### 3. 依存関係のインストール
```bash
docker compose exec php composer install
docker compose exec php npm install
```

### 4. 環境設定
```bash
# .envファイルの設定（MySQL用）
docker compose exec php cp .env.example .env
# 必要に応じて.envファイルを編集
```

### 5. データベースマイグレーション
```bash
docker compose exec php php artisan migrate
```

### 6. アセットのビルド
```bash
docker compose exec php npm run build
```

## 使用方法

### 1. ファイル操作
1. http://localhost:8081 にアクセス
2. ファイルを選択
3. 操作タイプを選択
4. 「処理実行」ボタンをクリック
5. 処理されたファイルが自動ダウンロード

### 2. ログ確認
1. 「ログ確認」リンクをクリック
2. 操作タイプ別にアコーディオン形式で表示
3. 「すべて開く/閉じる」ボタンで一括操作

### 3. データベース管理
1. http://localhost:8080 にアクセス (phpMyAdmin)
2. ユーザー名: root
3. パスワード: password
4. データベース: file_manipulator

## ファイル保存先

処理されたファイルは以下のディレクトリに保存されます：

```
storage/app/public/
├── file-reverse/     # リバース操作
├── file-copy/        # コピー操作
├── file-duplicate/   # 重複操作
└── file-replace/     # 置換操作
```

## ログ機能

### ファイルログ
- 場所: `storage/logs/file_manipulator/`
- 形式: `{operation}-{date}.log`

### データベースログ
- テーブル: `operation_logs`, `file_operations`
- 詳細な操作履歴とファイル情報を保存
- ダウンロード履歴の追跡

## 開発コマンド

```bash
# マイグレーション実行
docker compose exec php php artisan migrate

# マイグレーション状態確認
docker compose exec php php artisan migrate:status

# キャッシュクリア
docker compose exec php php artisan config:cache

# テスト実行
docker compose exec php php artisan test

# ログ確認
docker compose exec php tail -f storage/logs/laravel.log
```

## トラブルシューティング

### データベース接続エラー
```bash
# MySQL接続確認
docker compose exec mysql mysql -u root -ppassword -e "SHOW DATABASES;"

# 設定キャッシュクリア
docker compose exec php php artisan config:cache
```

### ファイル権限エラー
```bash
# ストレージ権限設定
docker compose exec php chmod -R 775 storage
docker compose exec php chmod -R 775 bootstrap/cache
```

## 更新履歴

- **v1.3.0**: MySQL8データベース機能追加、phpMyAdmin統合
- **v1.2.0**: 開閉式アコーディオンログ表示機能追加
- **v1.1.0**: ファイル保存先の整理、自動ダウンロード機能追加
- **v1.0.0**: 基本的なファイル操作機能実装
