<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileManipulatorService
{
    public function processFile(string $command, UploadedFile $file, array $params)
    {
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

            $filename = 'result_' . uniqid() . '_' . $originalFilename;
            $path = Storage::disk('local')->put("temp/{$filename}", $result);
            $fullPath = Storage::disk('local')->path("temp/{$filename}");

            // 処理成功ログ
            Log::channel($logChannel)->info("ファイル処理成功", [
                'command' => $command,
                'original_filename' => $originalFilename,
                'result_filename' => $filename,
                'original_size' => strlen($content),
                'result_size' => strlen($result),
                'processing_time' => microtime(true) - LARAVEL_START
            ]);

            return response()->download($fullPath)->deleteFileAfterSend();
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
}
