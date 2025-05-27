<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileManipulatorService
{
    public function processFile(string $command, UploadedFile $file, array $params)
    {
        $startTime = microtime(true);
        $logChannel = $this->getLogChannel($command);
        $originalFilename = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        // 処理開始ログ
        Log::channel($logChannel)->info("ファイル処理開始", [
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
                        'search_string' => $search,
                        'replace_string' => $replace,
                        'replace_count' => $replaceCount
                    ]);
                    break;
                default:
                    Log::channel($logChannel)->error("未対応のコマンド", ['command' => $command]);
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

            // 処理成功ログ
            Log::channel($logChannel)->info("ファイル処理成功", [
                'command' => $command,
                'original_filename' => $originalFilename,
                'result_filename' => $filename,
                'saved_path' => $relativePath,
                'original_size' => strlen($content),
                'result_size' => strlen($result),
                'processing_time' => microtime(true) - $startTime
            ]);

            return [
                'success' => true,
                'file_path' => $fullPath,
                'relative_path' => $relativePath,
                'filename' => $filename,
                'download_url' => asset('storage/' . $relativePath)
            ];
        } catch (\Exception $e) {
            // エラーログ
            Log::channel($logChannel)->error("ファイル処理エラー", [
                'command' => $command,
                'original_filename' => $originalFilename,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
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
