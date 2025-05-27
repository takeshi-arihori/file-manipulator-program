<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\OperationLog;
use App\Models\FileOperation;

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
            $filename = "{$timestamp}_{$originalFilename}";
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
                'mime_type' => $file->getMimeType(),
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
     * コマンドを正規化
     */
    private function normalizeCommand(string $command): string
    {
        $commandMap = [
            'reverse' => 'reverse',
            'copy' => 'copy',
            'duplicate-contents' => 'duplicate',
            'replace-string' => 'replace'
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
            'replace-string' => 'file_replace'
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
            'replace-string' => 'file-replace'
        ];

        return $directoryMap[$command] ?? 'file-other';
    }
}
