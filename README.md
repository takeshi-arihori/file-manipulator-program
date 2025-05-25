# PHPプログラミング演習

## プロジェクト概要
このプロジェクトは、PHPを使用した基本的なWebアプリケーションの演習プログラムです。ファイル操作とゲームの2つのプログラムを提供しています。

## プログラム一覧

### 1. ファイル操作プログラム
テキストファイルの内容を操作するためのWebアプリケーションです。

**主な機能**:
- ファイル内容の反転
- ファイルのコピー
- ファイル内容の複製
- 文字列置換

[詳細な使い方](src/file_manipulator.md)

### 2. 数字当てゲーム
最小値と最大値を指定して、その範囲内のランダムな数字を当てるゲームです。

**主な機能**:
- カスタマイズ可能な数値範囲
- ヒント機能
- ゲーム状態の保持

[詳細な使い方](src/guess_the_number.md)

## 環境要件

- PHP 8.3以上
- Webサーバー（Apache/Nginx）
- モダンブラウザ（Chrome, Firefox, Safari, Edge）

## セットアップ方法

1. リポジトリをクローン
```bash
git clone [リポジトリURL]
```

2. Webサーバーのドキュメントルートに配置
```bash
cp -r src/* /var/www/html/
```

3. 必要な権限を設定
```bash
chmod -R 755 /var/www/html
```

4. Webブラウザでアクセス
```
http://localhost/
```

## ディレクトリ構造

```
.
├── README.md
├── src/
│   ├── file_manipulator_program.php
│   ├── file_manipulator.md
│   ├── guess_the_number_game.php
│   ├── guess_the_number.md
│   ├── classes/
│   │   └── FileHandler.php
│   └── handlers/
│       ├── ReverseHandler.php
│       ├── CopyHandler.php
│       ├── DuplicateHandler.php
│       └── ReplaceHandler.php
└── docker/
    ├── Dockerfile
    └── php.ini
```

## 開発環境の構築

Dockerを使用して開発環境を構築する場合：

1. Dockerイメージのビルド
```bash
docker compose build
```

2. コンテナの起動
```bash
docker compose up -d
```

3. アプリケーションにアクセス
```
http://localhost:8081
```

## ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 作者

[あなたの名前]

## 更新履歴

- 2024-03-XX: 初期バージョンリリース
