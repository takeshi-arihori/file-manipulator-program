<?php
require_once __DIR__ . '/../classes/FileHandler.php';

class DuplicateHandler extends FileHandler
{
    private $duplicateCount;

    public function __construct($file, $duplicateCount)
    {
        parent::__construct($file);
        $this->duplicateCount = (int)$duplicateCount;
    }

    public function handle()
    {
        if (!$this->readFile()) {
            return false;
        }

        if ($this->duplicateCount < 1) {
            $this->message = '複製回数は1以上の整数を指定してください。';
            return false;
        }

        $duplicatedContent = str_repeat($this->inputContent, $this->duplicateCount);
        $this->downloadFile($duplicatedContent, 'duplicated_');
        return true;
    }
}
