<?php

class FileHandler
{
    protected $inputFile;
    protected $inputContent;
    protected $message;

    public function __construct($file)
    {
        $this->inputFile = $file;
        $this->message = '';
    }

    protected function readFile()
    {
        if (!isset($this->inputFile) || $this->inputFile['error'] !== UPLOAD_ERR_OK) {
            $this->message = 'ファイルのアップロードに失敗しました。';
            return false;
        }

        $this->inputContent = file_get_contents($this->inputFile['tmp_name']);
        if ($this->inputContent === false) {
            $this->message = 'ファイルの読み込みに失敗しました。';
            return false;
        }

        return true;
    }

    protected function downloadFile($content, $prefix)
    {
        $outputFilename = $prefix . $this->inputFile['name'];
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $outputFilename . '"');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
