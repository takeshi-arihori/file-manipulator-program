<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\OperationLog;
use App\Models\FileOperation;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class FileManipulatorService
{
    public function processFile(string $command, UploadedFile $file, array $params)
    {
        $startTime = microtime(true);
        $logChannel = $this->getLogChannel($command);
        $originalFilename = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        // 操作ログをDBに作成（初期状態）
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

        // 処理開始ログ
        Log::channel($logChannel)->info("ファイル処理開始", [
            'operation_log_id' => $operationLog->id,
            'command' => $command,
            'original_filename' => $originalFilename,
            'file_size' => $fileSize,
            'params' => $params,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        try {
            $content = file_get_contents($file->getRealPath());
            $result = '';

            switch ($command) {
                case 'reverse':
                    $result = strrev($content);
                    break;
                case 'copy':
                    $result = $content . $content;
                    break;
                case 'duplicate-contents':
                    $count = (int)($params['duplicate_count'] ?? 1);
                    $result = str_repeat($content, $count);
                    Log::channel($logChannel)->info("重複処理実行", [
                        'operation_log_id' => $operationLog->id,
                        'duplicate_count' => $count,
                        'original_size' => strlen($content),
                        'result_size' => strlen($result)
                    ]);
                    break;
                case 'replace-string':
                    $search = $params['search_string'] ?? '';
                    $replace = $params['replace_string'] ?? '';
                    $result = str_replace($search, $replace, $content);
                    $replaceCount = substr_count($content, $search);
                    Log::channel($logChannel)->info("文字列置換実行", [
                        'operation_log_id' => $operationLog->id,
                        'search_string' => $search,
                        'replace_string' => $replace,
                        'replace_count' => $replaceCount
                    ]);
                    break;
                case 'markdown-to-html':
                    // マークダウンファイルの拡張子チェック
                    $fileExtension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
                    if (!in_array($fileExtension, ['md', 'markdown'])) {
                        throw new \Exception('マークダウンファイル（.md または .markdown）をアップロードしてください。');
                    }

                    // GitHub Flavored Markdownの環境を作成（表、取り消し線、自動リンクなどをサポート）
                    $environment = new Environment([
                        'html_input' => 'strip',
                        'allow_unsafe_links' => false,
                    ]);

                    // 拡張機能を追加
                    $environment->addExtension(new CommonMarkCoreExtension());
                    $environment->addExtension(new GithubFlavoredMarkdownExtension());

                    $converter = new MarkdownConverter($environment);

                    $htmlContent = $converter->convert($content)->getContent();
                    $result = $this->wrapHtmlTemplate($htmlContent, $originalFilename);

                    Log::channel($logChannel)->info("マークダウン変換実行", [
                        'operation_log_id' => $operationLog->id,
                        'original_size' => strlen($content),
                        'html_size' => strlen($result),
                        'file_extension' => $fileExtension
                    ]);
                    break;
                default:
                    Log::channel($logChannel)->error("未対応のコマンド", [
                        'operation_log_id' => $operationLog->id,
                        'command' => $command
                    ]);
                    throw new \Exception('未対応のコマンドです。');
            }

            // 操作別ディレクトリに保存
            $directoryName = $this->getDirectoryName($command);
            $timestamp = now()->format('Y-m-d_H-i-s');

            // マークダウン変換の場合はファイル名を.htmlに変更
            if ($command === 'markdown-to-html') {
                $filename = "{$timestamp}_" . pathinfo($originalFilename, PATHINFO_FILENAME) . ".html";
            } else {
                $filename = "{$timestamp}_{$originalFilename}";
            }

            $relativePath = "{$directoryName}/{$filename}";

            // ディレクトリが存在しない場合は作成
            Storage::disk('public')->makeDirectory($directoryName);

            // ファイルを保存
            Storage::disk('public')->put($relativePath, $result);
            $fullPath = Storage::disk('public')->path($relativePath);

            $executionTime = microtime(true) - $startTime;

            // 操作ログを更新
            $operationLog->update([
                'output_filename' => $filename,
                'execution_time' => $executionTime,
                'file_path' => $relativePath,
                'file_size' => strlen($result)
            ]);

            // ファイル操作履歴をDBに保存
            FileOperation::create([
                'operation_log_id' => $operationLog->id,
                'original_filename' => $originalFilename,
                'stored_filename' => $filename,
                'file_path' => $relativePath,
                'operation_directory' => $directoryName,
                'file_size' => strlen($result),
                'mime_type' => $command === 'markdown-to-html' ? 'text/html' : $file->getMimeType(),
                'file_content_preview' => $this->getContentPreview($result),
                'is_downloaded' => false
            ]);

            // 処理成功ログ
            Log::channel($logChannel)->info("ファイル処理成功", [
                'operation_log_id' => $operationLog->id,
                'command' => $command,
                'original_filename' => $originalFilename,
                'result_filename' => $filename,
                'saved_path' => $relativePath,
                'original_size' => strlen($content),
                'result_size' => strlen($result),
                'processing_time' => $executionTime
            ]);

            return [
                'success' => true,
                'file_path' => $fullPath,
                'relative_path' => $relativePath,
                'filename' => $filename,
                'download_url' => asset('storage/' . $relativePath),
                'operation_log_id' => $operationLog->id
            ];
        } catch (\Exception $e) {
            // エラー時の操作ログ更新
            $operationLog->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
                'execution_time' => microtime(true) - $startTime
            ]);

            // エラーログ
            Log::channel($logChannel)->error("ファイル処理エラー", [
                'operation_log_id' => $operationLog->id,
                'command' => $command,
                'original_filename' => $originalFilename,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * HTMLテンプレートでマークダウン変換結果をラップ
     */
    private function wrapHtmlTemplate(string $htmlContent, string $originalFilename): string
    {
        $title = pathinfo($originalFilename, PATHINFO_FILENAME);

        return <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #2c3e50;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }
        h1 { border-bottom: 2px solid #3498db; padding-bottom: 0.3em; }
        h2 { border-bottom: 1px solid #bdc3c7; padding-bottom: 0.2em; }
        code {
            background-color: #f8f9fa;
            padding: 2px 4px;
            border-radius: 3px;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9em;
        }
        pre {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            overflow-x: auto;
        }
        pre code {
            background-color: transparent;
            padding: 0;
        }
        blockquote {
            border-left: 4px solid #3498db;
            margin: 0;
            padding-left: 20px;
            color: #7f8c8d;
            font-style: italic;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 1em 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        .generated-info {
            border-top: 1px solid #e9ecef;
            margin-top: 2em;
            padding-top: 1em;
            font-size: 0.9em;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    {$htmlContent}
    
    <div class="generated-info">
        <p>Generated from: {$originalFilename} | Date: {date('Y-m-d H:i:s')}</p>
    </div>
</body>
</html>
HTML;
    }

    /**
     * コマンドを正規化
     */
    private function normalizeCommand(string $command): string
    {
        $commandMap = [
            'reverse' => 'reverse',
            'copy' => 'copy',
            'duplicate-contents' => 'duplicate',
            'replace-string' => 'replace',
            'markdown-to-html' => 'markdown'
        ];

        return $commandMap[$command] ?? $command;
    }

    /**
     * ファイル内容のプレビューを取得（最初の200文字）
     */
    private function getContentPreview(string $content): string
    {
        return mb_substr($content, 0, 200);
    }

    /**
     * コマンドに対応するログチャンネルを取得
     */
    private function getLogChannel(string $command): string
    {
        $channelMap = [
            'reverse' => 'file_reverse',
            'copy' => 'file_copy',
            'duplicate-contents' => 'file_duplicate',
            'replace-string' => 'file_replace',
            'markdown-to-html' => 'file_markdown'
        ];

        return $channelMap[$command] ?? 'single';
    }

    /**
     * コマンドに対応するディレクトリ名を取得
     */
    private function getDirectoryName(string $command): string
    {
        $directoryMap = [
            'reverse' => 'file-reverse',
            'copy' => 'file-copy',
            'duplicate-contents' => 'file-duplicate',
            'replace-string' => 'file-replace',
            'markdown-to-html' => 'file-markdown'
        ];

        return $directoryMap[$command] ?? 'file-other';
    }
}
