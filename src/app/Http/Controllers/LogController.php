<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\OperationLog;
use App\Models\FileOperation;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $operation = $request->get('operation', 'all');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $logTypes = ['reverse', 'copy', 'duplicate', 'replace'];
        $logs = [];

        if ($operation === 'all') {
            foreach ($logTypes as $type) {
                $logs[$type] = $this->getLogsForOperation($type, $date);
            }
        } else {
            $logs[$operation] = $this->getLogsForOperation($operation, $date);
        }

        // ファイルベースのログも併せて取得（既存ログとの互換性のため）
        $fileLogs = $this->getFileBasedLogs($operation, $date, $logTypes);

        // DBログとファイルログをマージ
        foreach ($fileLogs as $type => $fileLogData) {
            if (!empty($fileLogData)) {
                if (!isset($logs[$type])) {
                    $logs[$type] = [];
                }
                $logs[$type] = array_merge($logs[$type], $fileLogData);
            }
        }

        return view('logs.index', compact('logs', 'operation', 'date', 'logTypes'));
    }

    private function getLogsForOperation($operation, $date)
    {
        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

        $operationLogs = OperationLog::with('fileOperations')
            ->where('operation_type', $operation)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $logs = [];
        foreach ($operationLogs as $log) {
            $logs[] = [
                'id' => $log->id,
                'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                'operation_type' => $log->operation_type,
                'operation_type_display' => $log->operation_type_display,
                'input_filename' => $log->input_filename,
                'output_filename' => $log->output_filename,
                'execution_time' => $log->execution_time,
                'status' => $log->status,
                'error_message' => $log->error_message,
                'file_path' => $log->file_path,
                'file_size' => $log->file_size,
                'operation_details' => $log->operation_details,
                'file_operations' => $log->fileOperations->map(function ($fileOp) {
                    return [
                        'id' => $fileOp->id,
                        'original_filename' => $fileOp->original_filename,
                        'stored_filename' => $fileOp->stored_filename,
                        'file_path' => $fileOp->file_path,
                        'operation_directory' => $fileOp->operation_directory,
                        'file_size' => $fileOp->file_size,
                        'formatted_file_size' => $fileOp->formatted_file_size,
                        'mime_type' => $fileOp->mime_type,
                        'file_content_preview' => $fileOp->file_content_preview,
                        'is_downloaded' => $fileOp->is_downloaded,
                        'downloaded_at' => $fileOp->downloaded_at?->format('Y-m-d H:i:s'),
                        'download_url' => asset('storage/' . $fileOp->file_path),
                    ];
                }),
                'level' => $log->status === 'error' ? 'error' : 'info',
                'message' => $this->formatLogMessage($log),
                'raw' => $this->formatRawLog($log),
                'source' => 'database'
            ];
        }

        return $logs;
    }

    private function getFileBasedLogs($operation, $date, $logTypes)
    {
        $logs = [];

        if ($operation === 'all') {
            foreach ($logTypes as $type) {
                $logs[$type] = $this->getFileLogsForOperation($type, $date);
            }
        } else {
            $logs[$operation] = $this->getFileLogsForOperation($operation, $date);
        }

        return $logs;
    }

    private function getFileLogsForOperation($operation, $date)
    {
        $logFile = storage_path("logs/file_manipulator/{$operation}-{$date}.log");

        if (!File::exists($logFile)) {
            return [];
        }

        $content = File::get($logFile);
        $lines = array_filter(explode("\n", $content));

        $logs = [];
        foreach ($lines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(\w+)\.(\w+): (.+)$/', $line, $matches)) {
                $logs[] = [
                    'timestamp' => $matches[1],
                    'level' => $matches[3],
                    'message' => $matches[4],
                    'raw' => $line,
                    'source' => 'file'
                ];
            }
        }

        return array_reverse($logs); // 新しいログを上に表示
    }

    private function formatLogMessage(OperationLog $log): string
    {
        $message = "ファイル処理";

        if ($log->status === 'error') {
            $message .= "エラー: {$log->error_message}";
        } else {
            $message .= "成功: {$log->input_filename}";
            if ($log->output_filename) {
                $message .= " → {$log->output_filename}";
            }
            if ($log->execution_time) {
                $message .= " (実行時間: {$log->execution_time}秒)";
            }
        }

        return $message;
    }

    private function formatRawLog(OperationLog $log): string
    {
        $timestamp = $log->created_at->format('Y-m-d H:i:s');
        $level = $log->status === 'error' ? 'ERROR' : 'INFO';
        $message = $this->formatLogMessage($log);

        return "[{$timestamp}] local.{$level}: {$message}";
    }
}
