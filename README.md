# File Manipulator Program

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

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: TailwindCSS, Alpine.js
- **Build Tool**: Vite
- **Container**: Docker & Docker Compose
- **Testing**: PHPUnit

## 環境要件

- PHP 8.2以上
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

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 更新履歴

- 2025-05-27: ログファイル一覧の開閉式アコーディオン実装
- 2025-05-27: ファイル保存先をstorage/app/public内の操作別ディレクトリに変更
- 2025-05-27: ログ機能の実装
- 2025-05-27: レイアウト修正とCSS設定の改善
- 2025-05-27: 初期バージョンリリース
