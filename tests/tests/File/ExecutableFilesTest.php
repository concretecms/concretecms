<?php

namespace Concrete\Tests\File;

use Concrete\Tests\TestCase;

class ExecutableFilesTest extends TestCase
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
            return !str_starts_with($file, '.git/')
                && !str_starts_with($file, 'concrete/vendor/')
                && !str_starts_with($file, 'packages/')
                && !str_starts_with($file, 'updates/')
                && !str_starts_with($file, 'build/node_modules')
            ;
        });
        sort($actual);
        $expected = [
            'concrete/bin/concrete',
            'concrete/bin/concrete5',
        ];
        $this->assertSame(
            $expected,
            $actual,
            'Checking that only selected files are executable'
        );
    }
}
