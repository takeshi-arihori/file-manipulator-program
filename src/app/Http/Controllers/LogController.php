<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

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

        return view('logs.index', compact('logs', 'operation', 'date', 'logTypes'));
    }

    private function getLogsForOperation($operation, $date)
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
                    'raw' => $line
                ];
            }
        }

        return array_reverse($logs); // 新しいログを上に表示
    }
}
