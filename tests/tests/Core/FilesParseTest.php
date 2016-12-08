<?php

class FilesParseTest extends \PHPUnit_Framework_TestCase
{

    public function testFilesParse()
    {
        foreach ($this->getPhpFiles(DIR_BASE_CORE . "/src") as $file) {
            $this->loadFile(array_shift($file));
        }
    }

    private function getPhpFiles($path)
    {
        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directory);
        return new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
    }

    private function loadFile($path)
    {
        ob_start();
        require_once $path;
        ob_end_clean();
    }

}
