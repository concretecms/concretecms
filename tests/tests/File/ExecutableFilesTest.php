<?php

namespace Concrete\Tests\File;

use PHPUnit_Framework_TestCase;

class ExecutableFilesTest extends PHPUnit_Framework_TestCase
{
    public function testExecutableFiles()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped('Testing executable files requires a Posix environment');
        }
        $rc = -1;
        $output = [];
        @exec('find ' . escapeshellarg(DIR_BASE) . ' -type f -executable', $output, $rc);
        if ($rc !== 0) {
            $this->markTestSkipped('Failed to retrieve the list of executable files (' . implode("\n", $output) . ')');
        }
        $output = array_map(function ($file) { return substr($file, strlen(DIR_BASE) + 1); }, $output);
        $actual = array_filter($output, function ($file) {
            return strpos($file, '.git/') !== 0
                && strpos($file, 'concrete/vendor/') !== 0
                && strpos($file, 'packages/') !== 0
                && strpos($file, 'updates/') !== 0
                && strpos($file, '.travis/') !== 0
            ;
        });
        sort($actual);
        $expected = [
            'concrete/bin/concrete5',
            'tests/assets/Docker/run-install.sh',
            'tests/assets/Docker/run-update.sh',
        ];
        $this->assertSame(
            $expected,
            $actual,
            'Checking that only selected files are executable'
        );
    }
}
