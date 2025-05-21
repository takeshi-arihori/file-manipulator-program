<?php
require_once __DIR__ . '/../classes/FileHandler.php';

class ReverseHandler extends FileHandler
{
    public function handle()
    {
        if (!$this->readFile()) {
            return false;
        }

        // ファイルの文字エンコーディングを検出
        $encoding = mb_detect_encoding($this->inputContent, ['UTF-8', 'SJIS', 'EUC-JP', 'ASCII'], true);
        if (!$encoding) {
            $encoding = 'UTF-8';
        }

        // 一旦UTF-8に変換してから処理
        $utf8Content = mb_convert_encoding($this->inputContent, 'UTF-8', $encoding);

        // 文字列を反転
        $reversedUtf8 = '';
        $length = mb_strlen($utf8Content, 'UTF-8');

        // マルチバイト文字に対応した反転処理
        for ($i = $length - 1; $i >= 0; $i--) {
            $reversedUtf8 .= mb_substr($utf8Content, $i, 1, 'UTF-8');
        }

        $this->downloadFile($reversedUtf8, 'reversed_');
        return true;
    }
}
