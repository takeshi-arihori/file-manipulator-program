<?php
require_once __DIR__ . '/../classes/FileHandler.php';

class ReplaceHandler extends FileHandler
{
    private $searchString;
    private $replaceString;

    public function __construct($file, $searchString, $replaceString)
    {
        parent::__construct($file);
        $this->searchString = $searchString;
        $this->replaceString = $replaceString;
    }

    public function handle()
    {
        if (!$this->readFile()) {
            return false;
        }

        if (empty($this->searchString)) {
            $this->message = '検索文字列を指定してください。';
            return false;
        }

        $replacedContent = str_replace($this->searchString, $this->replaceString, $this->inputContent);
        $this->downloadFile($replacedContent, 'replaced_');
        return true;
    }
}
