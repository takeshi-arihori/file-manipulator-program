<?php

namespace App\Http\Controllers;

use App\Services\FileManipulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\FileOperation;

class FileManipulatorController extends Controller
{
    protected FileManipulatorService $service;

    public function __construct(FileManipulatorService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('file-manipulator.index');
    }

    public function process(Request $request)
    {
        $request->validate([
            'input_file' => 'required|file',
            'command' => 'required|string',
        ]);

        $file = $request->file('input_file');
        $params = $request->all();

        try {
            $result = $this->service->processFile($params['command'], $file, $params);

            // ダウンロード履歴をDBに記録
            if (isset($result['operation_log_id'])) {
                FileOperation::where('operation_log_id', $result['operation_log_id'])
                    ->update([
                        'is_downloaded' => true,
                        'downloaded_at' => now()
                    ]);
            }

            // ファイルを自動ダウンロード
            return response()->download($result['file_path'], $result['filename']);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
