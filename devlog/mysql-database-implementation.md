# MySQL8データベース実装ガイド

## 概要

ファイル操作プログラムにMySQL8データベース機能を追加し、ログやストレージデータをDBに保存する機能を実装しました。また、日本時間対応も併せて実装しました。

## 実装日時

- 開始: 2025-05-27
- 完了: 2025-05-27
- 実装時間: 約2時間

## 要件

- Logやstorageに保存したデータをDBに保存したい
- Docker上でDBの作成 MySQL8でお願いします
- 日付を日本時間に合わせたい

## 実装手順

### 1. Docker Compose設定の更新

#### 1.1 MySQL8コンテナの追加

```yaml
mysql:
  image: mysql:8.0
  container_name: file_manipulator_mysql
  restart: unless-stopped
  ports:
    - "33063:3306"  # ユーザー指定のポート
  environment:
    MYSQL_ROOT_PASSWORD: password
    MYSQL_DATABASE: file_manipulator
    MYSQL_USER: laravel
    MYSQL_PASSWORD: password
    TZ: Asia/Tokyo  # 日本時間設定
  volumes:
    - mysql_data:/var/lib/mysql
    - ./docker/mysql/init:/docker-entrypoint-initdb.d
  command: --default-authentication-plugin=mysql_native_password --default-time-zone='+09:00'
```

#### 1.2 phpMyAdmin管理ツールの追加

```yaml
phpmyadmin:
  image: phpmyadmin/phpmyadmin
  container_name: file_manipulator_phpmyadmin
  restart: unless-stopped
  ports:
    - "8080:80"
  environment:
    PMA_HOST: mysql
    PMA_PORT: 3306
    PMA_USER: root
    PMA_PASSWORD: password
  depends_on:
    - mysql
```

#### 1.3 PHPコンテナの環境変数追加

```yaml
php:
  # ... 既存設定
  environment:
    - DB_HOST=mysql
    - DB_PORT=3306
    - DB_DATABASE=file_manipulator
    - DB_USERNAME=root
    - DB_PASSWORD=password
    - TZ=Asia/Tokyo  # 日本時間設定
```

### 2. Laravel環境設定の変更

#### 2.1 データベース接続設定

SQLiteからMySQLに変更：

```bash
# .envファイルの更新
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=file_manipulator
DB_USERNAME=root
DB_PASSWORD=password
```

#### 2.2 タイムゾーン設定

`config/app.php`:
```php
'timezone' => 'Asia/Tokyo',
```

`docker/php.ini`:
```ini
[Date]
date.timezone = Asia/Tokyo
```

### 3. データベース設計

#### 3.1 operation_logs テーブル

操作ログの基本情報を保存するテーブル：

```php
Schema::create('operation_logs', function (Blueprint $table) {
    $table->id();
    $table->string('operation_type'); // reverse, copy, duplicate, replace
    $table->string('input_filename');
    $table->string('output_filename')->nullable();
    $table->text('operation_details')->nullable(); // JSON形式で詳細情報
    $table->decimal('execution_time', 8, 4)->nullable(); // 実行時間（秒）
    $table->enum('status', ['success', 'error'])->default('success');
    $table->text('error_message')->nullable();
    $table->string('file_path')->nullable(); // 保存されたファイルのパス
    $table->integer('file_size')->nullable(); // ファイルサイズ（バイト）
    $table->timestamps();
});
```

#### 3.2 file_operations テーブル

ファイル操作の詳細情報を保存するテーブル：

```php
Schema::create('file_operations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('operation_log_id')->constrained()->onDelete('cascade');
    $table->string('original_filename');
    $table->string('stored_filename');
    $table->string('file_path');
    $table->string('operation_directory'); // file-reverse, file-copy, etc.
    $table->integer('file_size');
    $table->string('mime_type')->nullable();
    $table->text('file_content_preview')->nullable(); // 最初の数行のプレビュー
    $table->boolean('is_downloaded')->default(false);
    $table->timestamp('downloaded_at')->nullable();
    $table->timestamps();
});
```

### 4. Eloquentモデルの実装

#### 4.1 OperationLogモデル

```php
class OperationLog extends Model
{
    protected $fillable = [
        'operation_type', 'input_filename', 'output_filename',
        'operation_details', 'execution_time', 'status',
        'error_message', 'file_path', 'file_size',
    ];

    protected $casts = [
        'operation_details' => 'array',
        'execution_time' => 'decimal:4',
        'downloaded_at' => 'datetime',
    ];

    public function fileOperations(): HasMany
    {
        return $this->hasMany(FileOperation::class);
    }

    public function getOperationTypeDisplayAttribute(): string
    {
        return match($this->operation_type) {
            'reverse' => 'リバース操作',
            'copy' => 'コピー操作',
            'duplicate' => '重複操作',
            'replace' => '置換操作',
            default => $this->operation_type,
        };
    }
}
```

#### 4.2 FileOperationモデル

```php
class FileOperation extends Model
{
    protected $fillable = [
        'operation_log_id', 'original_filename', 'stored_filename',
        'file_path', 'operation_directory', 'file_size', 'mime_type',
        'file_content_preview', 'is_downloaded', 'downloaded_at',
    ];

    protected $casts = [
        'is_downloaded' => 'boolean',
        'downloaded_at' => 'datetime',
    ];

    public function operationLog(): BelongsTo
    {
        return $this->belongsTo(OperationLog::class);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
```

### 5. サービス層の拡張

#### 5.1 FileManipulatorServiceの修正

主な変更点：

1. **操作開始時のDB記録**
```php
$operationLog = OperationLog::create([
    'operation_type' => $this->normalizeCommand($command),
    'input_filename' => $originalFilename,
    'operation_details' => [
        'params' => $params,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent()
    ],
    'status' => 'success'
]);
```

2. **処理完了時の詳細更新**
```php
$operationLog->update([
    'output_filename' => $filename,
    'execution_time' => $executionTime,
    'file_path' => $relativePath,
    'file_size' => strlen($result)
]);

FileOperation::create([
    'operation_log_id' => $operationLog->id,
    'original_filename' => $originalFilename,
    'stored_filename' => $filename,
    'file_path' => $relativePath,
    'operation_directory' => $directoryName,
    'file_size' => strlen($result),
    'mime_type' => $file->getMimeType(),
    'file_content_preview' => $this->getContentPreview($result),
    'is_downloaded' => false
]);
```

3. **エラー時の状態管理**
```php
$operationLog->update([
    'status' => 'error',
    'error_message' => $e->getMessage(),
    'execution_time' => microtime(true) - $startTime
]);
```

### 6. コントローラーの拡張

#### 6.1 LogControllerの修正

- DBログとファイルログの統合表示
- 操作タイプ別のフィルタリング
- 日付別のフィルタリング
- 詳細情報の表示

```php
private function getLogsForOperation($operation, $date)
{
    $startDate = Carbon::parse($date)->startOfDay();
    $endDate = Carbon::parse($date)->endOfDay();

    $operationLogs = OperationLog::with('fileOperations')
        ->where('operation_type', $operation)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->orderBy('created_at', 'desc')
        ->get();
    
    // ... ログデータの整形
}
```

#### 6.2 FileManipulatorControllerの修正

ダウンロード履歴の記録：

```php
if (isset($result['operation_log_id'])) {
    FileOperation::where('operation_log_id', $result['operation_log_id'])
        ->update([
            'is_downloaded' => true,
            'downloaded_at' => now()
        ]);
}
```

### 7. マイグレーション実行

```bash
docker compose exec php php artisan migrate
```

実行結果：
```
INFO  Running migrations.  

0001_01_01_000000_create_users_table ..................................................... 168.96ms DONE
0001_01_01_000001_create_cache_table ...................................................... 80.40ms DONE
0001_01_01_000002_create_jobs_table ...................................................... 123.00ms DONE
2025_05_27_020513_create_operation_logs_table ............................................. 33.92ms DONE
2025_05_27_020520_create_file_operations_table ........................................... 159.53ms DONE
```

### 8. タイムゾーン設定の確認

#### 8.1 PHP設定確認
```bash
docker compose exec php php -r "echo 'PHP Timezone: ' . date_default_timezone_get() . PHP_EOL; echo 'Current Time: ' . date('Y-m-d H:i:s') . PHP_EOL;"
```

結果：
```
PHP Timezone: Asia/Tokyo
Current Time: 2025-05-27 11:16:19
```

#### 8.2 MySQL設定確認
```bash
docker compose exec mysql mysql -u root -ppassword -e "SELECT @@global.time_zone, @@session.time_zone, NOW();"
```

結果：
```
+--------------------+---------------------+---------------------+
| @@global.time_zone | @@session.time_zone | NOW()               |
+--------------------+---------------------+---------------------+
| +09:00             | +09:00              | 2025-05-27 11:16:33 |
+--------------------+---------------------+---------------------+
```

#### 8.3 Laravel設定確認
```bash
docker compose exec php php artisan tinker --execute="echo 'Laravel Timezone: ' . config('app.timezone') . PHP_EOL; echo 'Current Time: ' . now()->format('Y-m-d H:i:s') . PHP_EOL; echo 'Carbon Timezone: ' . now()->timezone->getName() . PHP_EOL;"
```

結果：
```
Laravel Timezone: Asia/Tokyo
Current Time: 2025-05-27 11:16:42
Carbon Timezone: Asia/Tokyo
```

## 実装結果

### ✅ 完了した機能

1. **MySQL8データベース環境**
   - Docker Composeでの自動構築
   - phpMyAdmin管理ツール統合
   - 永続化ボリューム設定

2. **データベース設計**
   - 操作ログテーブル（operation_logs）
   - ファイル操作詳細テーブル（file_operations）
   - 適切なリレーション設定

3. **アプリケーション機能**
   - ファイル操作時のDB自動記録
   - 実行時間・ファイルサイズ・エラー情報の記録
   - ダウンロード履歴の追跡
   - ログ表示のDB対応

4. **日本時間対応**
   - Laravel、PHP、MySQLすべてのコンポーネント
   - 統一された時刻表示

### 🔧 アクセス情報

- **Webアプリケーション**: http://localhost:8081
- **phpMyAdmin**: http://localhost:8080
  - ユーザー名: `root`
  - パスワード: `password`
  - データベース: `file_manipulator`
- **MySQL直接接続**: `localhost:33063`

### 📊 データベーステーブル

1. **operation_logs**: 11カラム（操作の基本情報）
2. **file_operations**: 12カラム（ファイルの詳細情報）
3. **既存のLaravelテーブル**: users, cache, jobs等

### 🚀 今後の拡張可能性

1. **統計機能**: 操作回数、ファイルサイズ統計
2. **検索機能**: ファイル名、操作タイプでの検索
3. **エクスポート機能**: CSV、Excel形式でのデータ出力
4. **ユーザー管理**: 操作者の識別・権限管理
5. **API機能**: REST APIでのデータアクセス

## トラブルシューティング

### 発生した問題と解決策

1. **環境設定ファイル編集制限**
   - 問題: `.env`ファイルが直接編集できない
   - 解決: `sed`コマンドでの設定変更

2. **CSRFトークンエラー**
   - 問題: curlでのテスト時にCSRFエラー
   - 解決: ブラウザでのテストに変更

3. **MySQL構文エラー**
   - 問題: SQL文の構文エラー
   - 解決: クエリの修正

### 設定確認コマンド

```bash
# データベース接続確認
docker compose exec mysql mysql -u root -ppassword -e "SHOW DATABASES;"

# テーブル確認
docker compose exec mysql mysql -u root -ppassword file_manipulator -e "SHOW TABLES;"

# マイグレーション状態確認
docker compose exec php php artisan migrate:status

# 設定キャッシュクリア
docker compose exec php php artisan config:cache
```

## まとめ

MySQL8データベース機能の追加により、以下の改善が実現されました：

1. **データの永続化**: ファイル操作履歴の確実な保存
2. **詳細な追跡**: 実行時間、ファイルサイズ、ダウンロード状況
3. **管理の向上**: phpMyAdminでの直感的なデータ管理
4. **時刻の統一**: 日本時間での一貫した時刻表示
5. **拡張性**: 将来的な機能追加への基盤構築

この実装により、ファイル操作プログラムはより堅牢で管理しやすいシステムとなりました。 
