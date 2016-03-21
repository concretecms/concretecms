<?php

namespace Concrete\Tests\Core\File\Service;

use Core;
use Exception;
use Concrete\Core\File\Service\Zip;

class ZipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\File\Service\Zip
     */
    protected $zipHelper;

    protected $sourceDir;
    protected $destDir;
    protected $zipFile;

    protected $fileSystemProblem;
    protected $delDirs;
    protected $delFiles;

    protected function setUp()
    {
        $this->zipHelper = Core::make('helper/zip');
        $this->delDirs = array();
        $this->delFiles = array();
        try {
            $tempDir = @sys_get_temp_dir();
            if (!is_dir($tempDir) || !is_writable($tempDir)) {
                throw new Exception('Temporary directory not found or not writable');
            }
            // Create source directory and data.
            $this->sourceDir = @tempnam($tempDir, 'c:5');
            @unlink($this->sourceDir);
            if (@mkdir($this->sourceDir) === false) {
                throw new Exception('Failed to create a temporary directory');
            }
            $this->delDirs[] = $this->sourceDir;
            if (@file_put_contents($this->sourceDir.'/root.txt', 'Root') === false) {
                throw new Exception('Failed to create a temporary file');
            }
            $this->delFiles[] = $this->sourceDir.'/root.txt';
            if (@file_put_contents($this->sourceDir.'/.root.txt', 'Root hidden') === false) {
                throw new Exception('Failed to create a temporary file');
            }
            $this->delFiles[] = $this->sourceDir.'/root.txt';
            if (@mkdir($this->sourceDir.'/inner') === false) {
                throw new Exception('Failed to create a temporary directory');
            }
            $this->delDirs[] = $this->sourceDir.'/inner';
            if (@mkdir($this->sourceDir.'/inner/empty') === false) {
                throw new Exception('Failed to create a temporary directory');
            }
            $this->delDirs[] = $this->sourceDir.'/inner/empty';
            if (@file_put_contents($this->sourceDir.'/inner/file with space.txt', 'Inner file with space') === false) {
                throw new Exception('Failed to create a temporary file');
            }
            $this->delFiles[] = $this->sourceDir.'/inner/file with space.txt';
            if (@mkdir($this->sourceDir.'/.innerHidden') === false) {
                throw new Exception('Failed to create a temporary directory');
            }
            $this->delDirs[] = $this->sourceDir.'/.innerHidden';
            if (@file_put_contents($this->sourceDir.'/.innerHidden/underHidden.txt', 'File under hidden directory') === false) {
                throw new Exception('Failed to create a temporary file');
            }
            $this->delFiles[] = $this->sourceDir.'/.innerHidden/underHidden.txt';
            $this->destDir = @tempnam($tempDir, 'c5');
            @unlink($this->destDir);
            if (@mkdir($this->destDir) === false) {
                throw new Exception('Failed to create a temporary directory');
            }
            $this->delDirs[] = $this->destDir;
            $this->zipFile = @tempnam($tempDir, 'c5');
            if ($this->zipFile === false) {
                throw new Exception('Failed to create a temporary file');
            }
            $this->delFiles[] = $this->zipFile;
            $this->delFiles[] = $this->destDir.'/root.txt';
            $this->delDirs[] = $this->destDir.'/inner';
            $this->delFiles[] = $this->destDir.'/inner/file with space.txt';
            $this->fileSystemProblem = null;
        } catch (Exception $x) {
            $this->fileSystemProblem = $x->getMessage();
        }
    }

    protected function tearDown()
    {
        foreach ($this->delFiles as $delFile) {
            @unlink($delFile);
        }
        $this->delFiles = array();
        foreach (array_reverse($this->delDirs) as $delDir) {
            @rmdir($delDir);
        }
        $this->delDirs = array();
    }

    public function useNativeProvider()
    {
        return array(
            array(false, false),
            array(true, false),
            array(false, true),
            array(true, true),
        );
    }

    /**
     * @dataProvider useNativeProvider
     */
    public function testZip($useNativeCommands, $includeDotFiles)
    {
        if ($this->fileSystemProblem !== null) {
            $this->markTestIncomplete('Error setting up files and directories');

            return;
        }
        $zh = $this->zipHelper;
        if ($useNativeCommands) {
            if (!$zh->isNativeCommandAvailable('zip') || !$zh->isNativeCommandAvailable('unzip')) {
                $this->markTestIncomplete('Native zip/unzip commands are not available');

                return;
            }
            $zh->enableNativeCommands();
        } else {
            $zh->disableNativeCommands();
        }
        $zh->zip($this->sourceDir, $this->zipFile, compact('includeDotFiles'));
        $zh->unzip($this->zipFile, $this->destDir);
        $this->assertFileExists($this->destDir.'/root.txt');
        $this->assertSame('Root', file_get_contents($this->destDir.'/root.txt'));
        $this->assertFileExists($this->destDir.'/inner/file with space.txt');
        $this->assertSame('Inner file with space', file_get_contents($this->destDir.'/inner/file with space.txt'));
        $this->assertTrue(is_dir($this->destDir.'/inner/empty'), 'Checking empty dir re-created');
        if ($includeDotFiles) {
            $this->assertFileExists($this->destDir.'/.root.txt');
            $this->assertSame('Root hidden', file_get_contents($this->destDir.'/.root.txt'));
            $this->assertFileExists($this->destDir.'/.innerHidden/underHidden.txt');
            $this->assertSame('File under hidden directory', file_get_contents($this->destDir.'/.innerHidden/underHidden.txt'));
        } else {
            $this->assertFileNotExists($this->destDir.'/.root.txt');
            $this->assertFileNotExists($this->destDir.'/.innerHidden');
            $this->assertFileNotExists($this->destDir.'/.innerHidden/underHidden.txt');
        }
    }
}
