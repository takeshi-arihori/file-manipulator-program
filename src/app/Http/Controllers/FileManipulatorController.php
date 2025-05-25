<?php

namespace App\Http\Controllers;

use App\Services\FileManipulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $download = $this->service->processFile($params['command'], $file, $params);
            return $download;
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
