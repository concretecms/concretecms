<?php

namespace Concrete\Tests\Database;

use Concrete\Tests\TestCase;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ReadWriteMethodUsageTest extends TestCase
{
    public function testFirstPartyCodeUsesExplicitReadWriteMethods()
    {
        $root = dirname(__DIR__, 3);
        $ignoredFiles = [
            realpath($root . '/concrete/src/Database/Connection/Connection.php'),
            realpath($root . '/tests/tests/Database/DatabaseTest.php'),
            realpath(__FILE__),
        ];
        $violations = [];

        foreach (['concrete', 'application', 'packages', 'tests'] as $directory) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root . '/' . $directory, FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                $path = $file->getPathname();
                if ($file->getExtension() !== 'php'
                    || strpos($path, $root . '/concrete/vendor/') === 0
                    || in_array(realpath($path), $ignoredFiles, true)
                ) {
                    continue;
                }

                $source = $this->stripComments(file_get_contents($path));
                $writePattern = '/->(?:executeQuery|fetchAllAssociative|fetchAssociative|fetchOne|fetchFirstColumn|fetchColumn)' .
                    '\\s*\\(\\s*([\'"])\\s*(?:INSERT|UPDATE|DELETE|REPLACE|TRUNCATE|ALTER|CREATE|DROP|RENAME|' .
                    'GRANT|REVOKE|CALL|SET\\b|LOCK|UNLOCK)/i';
                $legacyPattern = '/(?:\\$(?:db|connection|conn|cn|database)|\\$this->connection)' .
                    '\\s*->\\s*(?:query|Execute)\\s*\\(/';

                $this->collectViolations($violations, $path, $source, $writePattern, 'write SQL passed to a read API');
                $this->collectViolations($violations, $path, $source, $legacyPattern, 'deprecated mixed-purpose database API');
            }
        }

        $this->assertSame([], $violations, implode("\n", $violations));
    }

    private function stripComments($source)
    {
        $result = '';
        foreach (token_get_all($source) as $token) {
            if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                $result .= preg_replace('/[^\\r\\n]/', ' ', $token[1]);
            } else {
                $result .= is_array($token) ? $token[1] : $token;
            }
        }

        return $result;
    }

    private function collectViolations(array &$violations, $path, $source, $pattern, $description)
    {
        if (!preg_match_all($pattern, $source, $matches, PREG_OFFSET_CAPTURE)) {
            return;
        }
        foreach ($matches[0] as $match) {
            $line = substr_count(substr($source, 0, $match[1]), "\n") + 1;
            $violations[] = $path . ':' . $line . ': ' . $description;
        }
    }
}

