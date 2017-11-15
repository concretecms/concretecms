<?php

namespace Concrete\Tests\File;

use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class FilesParseTest extends PHPUnit_Framework_TestCase
{
    public function testFilesParse()
    {
        foreach ($this->getPhpFiles(DIR_BASE_CORE . '/src') as $file) {
            $this->loadFile(array_shift($file));
        }
    }

    private function getPhpFiles($path)
    {
        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directory);
        $phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        // Loop through the php files in the project and yield out the ones that should be tested
        foreach ($phpFiles as $file) {
            if ($this->shouldTest(head($file))) {
                yield $file;
            }
        }
    }

    private function loadFile($path)
    {
        ob_start();
        require_once $path;
        ob_end_clean();
    }

    private function shouldTest($path)
    {
        $filename = basename($path);

        // Ignore meta files for IDE's
        if ($filename == '.phpstorm.meta.php' || $filename == '__IDE_SYMBOLS__.php') {
            return false;
        }

        return true;
    }
}
