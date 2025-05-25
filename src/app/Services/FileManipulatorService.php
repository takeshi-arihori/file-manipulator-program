<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileManipulatorService
{
    public function processFile(string $command, UploadedFile $file, array $params)
    {
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
                break;
            case 'replace-string':
                $search = $params['search_string'] ?? '';
                $replace = $params['replace_string'] ?? '';
                $result = str_replace($search, $replace, $content);
                break;
            default:
                throw new \Exception('未対応のコマンドです。');
        }
        $filename = 'result_' . uniqid() . '_' . $file->getClientOriginalName();
        $path = Storage::disk('local')->put("temp/{$filename}", $result);
        $fullPath = Storage::disk('local')->path("temp/{$filename}");
        return response()->download($fullPath)->deleteFileAfterSend();
    }
}
