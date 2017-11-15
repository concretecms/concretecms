<?php

namespace Concrete\TestHelpers\Support;

use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

abstract class CodingStyleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var string[]|null
     */
    private static $phpFiles = null;

    public function phpFilesProvider()
    {
        $result = [];
        foreach (static::getPhpFiles() as $f) {
            $result[] = [$f];
        }

        return $result;
    }

    /**
     * @return string[]
     */
    protected static function getPhpFiles()
    {
        if (self::$phpFiles === null) {
            $files = [];
            $baseDir = rtrim(str_replace('/', DIRECTORY_SEPARATOR, DIR_BASE_CORE), DIRECTORY_SEPARATOR);
            $directoryIterator = new RecursiveDirectoryIterator($baseDir);
            $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($iterator as $f) {
                /* @var \SplFileInfo $f */
                if (!$f->isFile()) {
                    continue;
                }
                $filename = $f->getFilename();
                if ($filename[0] === '.' || strcasecmp(substr($filename, -4), '.php') !== 0) {
                    continue;
                }
                $fullPath = $f->getRealPath();
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($fullPath, strlen($baseDir)));
                if (strpos($relativePath, '/.') !== false) {
                    continue;
                }
                if (strpos($relativePath, '/vendor/') === 0) {
                    continue;
                }
                $files[] = $fullPath;
            }
            self::$phpFiles = $files;
        }

        return self::$phpFiles;
    }
}
