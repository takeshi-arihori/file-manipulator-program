<?php
require_once __DIR__ . '/../classes/FileHandler.php';

class CopyHandler extends FileHandler
{
    public function handle()
    {
        if (!$this->readFile()) {
            return false;
        }

        $this->downloadFile($this->inputContent, 'copied_');
        return true;
    }
}
