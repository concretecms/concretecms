<?php

namespace Concrete\Tests\Update;

use PHPUnit_Framework_TestCase;
use RuntimeException;

class NameAlreadyInUseTest extends PHPUnit_Framework_TestCase
{
    /**
     * The root directory of concrete5, with '/' as directory separator, without any leading '/'.
     *
     * @var string
     */
    private $webroot;

    public function setUp()
    {
        $this->webroot = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', DIR_BASE), '/');
    }

    /**
     * Let's load all the php-code only files, to be sure that we don't have errors like this:
     * Cannot use FillyQualifiedClassName as ClassAliasName because the name is already in use.
     *
     * @doesNotPerformAssertions
     */
    public function testNoNameInUseErrorException()
    {
        $this->loadFiles('');
    }

    /**
     * @param string $directoryRelativePath
     */
    private function loadFiles($directoryRelativePath)
    {
        if (!$this->shouldParseDirectory($directoryRelativePath)) {
            return;
        }
        $prefix = $directoryRelativePath === '' ? '' : "{$directoryRelativePath}/";
        $directoryHandle = opendir($this->webroot . '/' . $directoryRelativePath);
        if ($directoryHandle === false) {
            throw new RuntimeException('Failed to open the directory!');
        }
        try {
            $subdirectoryRelativePaths = [];
            while (($itemName = readdir($directoryHandle)) !== false) {
                if ($itemName === '.' || $itemName === '..') {
                    continue;
                }
                $itemRelativePath = $prefix . $itemName;
                if (is_dir($this->webroot . '/' . $itemRelativePath)) {
                    $subdirectoryRelativePaths[] = $itemRelativePath;
                } else {
                    $this->loadFile($itemRelativePath);
                }
            }
        } finally {
            closedir($directoryHandle);
        }
        foreach ($subdirectoryRelativePaths as $subdirectoryRelativePath) {
            $this->loadFiles($subdirectoryRelativePath);
        }
    }

    /**
     * @param string $directoryRelativePath
     *
     * @return bool
     */
    private function shouldParseDirectory($directoryRelativePath)
    {
        if ($directoryRelativePath !== '' && $directoryRelativePath !== 'concrete' && strpos($directoryRelativePath, 'concrete/') !== 0) {
            // Let's just parse the webroot and the concrete directory
            return false;
        }
        if (in_array($directoryRelativePath, [
            // These directories don't contain PHP-only files
            'concrete/bin',
            'concrete/bootstrap',
            'concrete/config',
            'concrete/css',
            'concrete/elements',
            'concrete/images',
            'concrete/js',
            'concrete/mail',
            'concrete/routes',
            'concrete/single_pages',
            'concrete/tools',
            'concrete/vendor',
            'concrete/views',
        ], true)) {
            return false;
        }
        if (preg_match('%^concrete/blocks/\w+/(form|templates|src|tools)$%', $directoryRelativePath)) {
            // These directories don't contain PHP-only files
            return false;
        }

        return true;
    }

    /**
     * @param string $fileRelativePath
     */
    private function loadFile($fileRelativePath)
    {
        if ($this->shouldLoadFile($fileRelativePath)) {
            require_once "{$this->webroot}/{$fileRelativePath}";
        }
    }

    /**
     * @param string $relpath
     * @param mixed $fileRelativePath
     *
     * @return bool
     */
    private function shouldLoadFile($fileRelativePath)
    {
        if (!preg_match('/.\.php$/', $fileRelativePath)) {
            // Let's just load PHP files
            return false;
        }
        if (strpos($fileRelativePath, 'concrete/') !== 0) {
            // Let's just load PHP files in the concrete directory
            return false;
        }
        if (preg_match('%^concrete/(attributes|authentication|blocks|geolocation)/\w+/%', $fileRelativePath)) {
            // For attributes, blocks, ...: let's just load their controllers
            return (bool) preg_match('%^concrete/(attributes|authentication|blocks|geolocation)/\w+/controller\.php%', $fileRelativePath);
        }
        if (preg_match('%^concrete/themes/\w+/%', $fileRelativePath)) {
            // For themes: let's just load their controllers
            return (bool) preg_match('%^concrete/themes/\w+/page_theme\.php%', $fileRelativePath);
        }

        switch ($fileRelativePath) {
            case 'concrete/dispatcher.php':
            case 'concrete/src/Support/.phpstorm.meta.php':
            case 'concrete/src/Support/__IDE_SYMBOLS__.php':
                return false;
            default:
                return true;
        }
    }
}
