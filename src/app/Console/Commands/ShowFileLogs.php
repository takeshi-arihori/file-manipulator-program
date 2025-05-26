<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ShowFileLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:show-file {operation?} {--date=} {--lines=20}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ファイル操作ログを表示します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $operation = $this->argument('operation');
        $date = $this->option('date') ?: date('Y-m-d');
        $lines = $this->option('lines');

        $logTypes = ['reverse', 'copy', 'duplicate', 'replace'];

        if ($operation && !in_array($operation, $logTypes)) {
            $this->error("無効な操作タイプです。利用可能: " . implode(', ', $logTypes));
            return 1;
        }

        $operations = $operation ? [$operation] : $logTypes;

        foreach ($operations as $op) {
            $this->showLogForOperation($op, $date, $lines);
        }

        return 0;
    }

    private function showLogForOperation($operation, $date, $lines)
    {
        $logFile = storage_path("logs/file_manipulator/{$operation}-{$date}.log");

        if (!File::exists($logFile)) {
            $this->warn("ログファイルが見つかりません: {$operation}-{$date}.log");
            return;
        }

        $this->info("=== {$operation} ログ ({$date}) ===");

        $content = File::get($logFile);
        $logLines = array_filter(explode("\n", $content));

        if (empty($logLines)) {
            $this->warn("ログエントリがありません");
            return;
        }

        $displayLines = array_slice($logLines, -$lines);

        foreach ($displayLines as $line) {
            $this->line($line);
        }

        $this->newLine();
    }
}
