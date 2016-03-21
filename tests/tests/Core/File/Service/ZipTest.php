<?php

namespace Concrete\Tests\Core\File\Service;

use Core;
use Exception;
use Concrete\Core\File\Service\Zip;
use Illuminate\Filesystem\Filesystem;

class ZipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\File\Service\Zip
     */
    protected $zipHelper;

    protected $rootDir = null;

    protected $fileSystemProblem = null;

    protected function getDirectories()
    {
        return array(
            'dir1' => false,
            'dir1/empty' => false,
            'dir1/dir1.1' => false,
            'dir1/dir1.2' => false,
            'dir1/dir1.2/dir1.2.1' => false,
            'dir1/dir1.2/.dir1.2.3' => true,
            'dir1/dir1.2/.dir1.2.3/dir1.2.4' => true,
            '.dir2' => true,
            '.dir2/dir2.1' => true,
            '.dir2/dir2.2' => true,
            '.dir2/dir2.2/dir2.2.1' => true,
            '.dir2/dir2.2/.dir2.2.3' => true,
            'dir3' => false,
            'dir4' => false,
            'dir4/dir41' => false,
            'dir5' => false,
            'dir5/.dir51' => true,
        );
    }

    protected function getFiles()
    {
        return array(
            'root.txt' => false,
            'dir1/sub.txt' => false,
            'dir1/.hidden.txt' => true,
            'dir1/dir1.2/dir1.2.1/good.txt' => false,
            'dir1/dir1.2/dir1.2.1/.bad.txt' => true,
            'dir1/dir1.2/.dir1.2.3/dir1.2.4/hide.txt' => true,
            '.dir2/hideByBath2.txt' => true,
            '.dir2/dir2.2/.dir2.2.3/hideByBath2.2.txt' => true,
            '.dir2/dir2.2/dir2.2.1/hide.txt' => true,
            'dir4/dir41/ok' => false,
            'dir4/dir41/.ko' => true,
            'dir5/.dir51/hidden.txt' => true,
        );
    }

    protected function setUp()
    {
        $this->zipHelper = Core::make('helper/zip');
        $this->workDir = null;
        try {
            $tempDir = @sys_get_temp_dir();
            if (!is_dir($tempDir) || !is_writable($tempDir)) {
                throw new Exception('Temporary directory not found or not writable');
            }
            $wd = @tempnam($tempDir, 'c5');
            @unlink($wd);
            if (@mkdir($wd) === false) {
                throw new Exception('Failed to create a temporary directory');
            }
            $this->workDir = $wd;
            $source = $wd.'/source';
            if (!@mkdir($source)) {
                throw new Exception('Failed to create a temporary directory');
            }
            foreach ($this->getDirectories() as $rel => $hidden) {
                $abs = $source.'/'.$rel;
                if (!@mkdir($abs)) {
                    throw new Exception('Failed to create a temporary directory');
                }
            }
            foreach ($this->getFiles() as $rel => $hidden) {
                $abs = $source.'/'.$rel;
                if (!@file_put_contents($abs, "This is the content of $rel")) {
                    throw new Exception('Failed to create a temporary file');
                }
            }
            $destination = $wd.'/destination';
            if (!@mkdir($destination)) {
                throw new Exception('Failed to create a temporary directory');
            }
            $this->fileSystemProblem = null;
        } catch (Exception $x) {
            $this->fileSystemProblem = $x->getMessage();
        }
    }

    protected function tearDown()
    {
        if ($this->workDir !== null) {
            id(new Filesystem())->deleteDirectory($this->workDir);
            $this->workDir = null;
        }
    }

    public function providerTestZip()
    {
        return array(
            array(false, false),
            array(true, false),
            array(false, true),
            array(true, true),
        );
    }

    /**
     * @dataProvider providerTestZip
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
        $zh->zip($this->workDir.'/source', $this->workDir.'/file.zip', compact('includeDotFiles'));
        $zh->unzip($this->workDir.'/file.zip', $this->workDir.'/destination');
        foreach ($this->getDirectories() as $rel => $hidden) {
            $abs = $this->workDir.'/destination/'.$rel;
            if ($hidden && !$includeDotFiles) {
                $this->assertFileNotExists($abs);
            } else {
                $this->assertFileExists($abs);
                $this->assertTrue(is_dir($abs));
            }
        }
        foreach ($this->getFiles() as $rel => $hidden) {
            $abs = $this->workDir.'/destination/'.$rel;
            if ($hidden && !$includeDotFiles) {
                $this->assertFileNotExists($abs);
            } else {
                $this->assertFileExists($abs);
                $this->assertTrue(is_file($abs));
                $this->assertSame("This is the content of $rel", file_get_contents($abs));
            }
        }
    }
}
