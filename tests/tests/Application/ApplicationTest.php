<?php

namespace Concrete\Tests\Application;

use PHPUnit\Framework\TestCase;
use Concrete\Core\Http\Request;
use Concrete\Core\Application\Application;

class ApplicationTest extends TestCase
{
    /** @var \Concrete\Core\Http\Request|null */
    private $oldRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->oldRequest = Request::getInstance();
    }

    protected function tearDown(): void
    {
        // Restore the original Request instance even if a case failed
        if ($this->oldRequest) {
            Request::setInstance($this->oldRequest);
        }
        parent::tearDown();
    }

    /**
     * @dataProvider scriptNameProvider
     */
    public function testAppRelativePath(string $scriptName, string $expectedRelativePath): void
    {
        // Swap in a synthetic Request with the SCRIPT_NAME we want to test
        Request::setInstance(new Request(
            [],
            [],
            [],
            [],
            [],
            [
                'HTTP_HOST'   => 'www.requestdomain.com',
                'SCRIPT_NAME' => $scriptName,
            ]
        ));

        $app = new Application();
        $app->detectEnvironment([]); // triggers code that sets ['app_relative_path']

        $this->assertSame(
            $expectedRelativePath,
            $app['app_relative_path'],
            sprintf('SCRIPT_NAME: %s', $scriptName)
        );
    }

    /**
     * Cases reflect the current implementation:
     *   - Finds "index.php" in SCRIPT_NAME
     *   - If position > 0, subtracts 1
     *   - Takes substring up to that position
     *   - Normalizes slashes and trims trailing '/'
     */
    public static function scriptNameProvider(): array
    {
        return [
            // Web root (correct)
            'root index.php' => [
                '/index.php',
                '', // expected
            ],

            // Some configurations give us this incorrectly? At least this happened in my local
            // nginx tests
            'double index.php' => [
                '/index.php/index.php',
                '', // expected
            ],

            // Simple subdir (correct)
            'subdir index.php' => [
                '/subdir/index.php',
                '/subdir',
            ],

            // Double slash before dispatcher (subtract-1 removes trailing slash)
            'double slash before index.php' => [
                '/deep/subdir//index.php',
                '/deep/subdir',
            ],

            // Edge case: mistaken “11index.php” at web root (off-by-one effect)
            'webroot but SCRIPT_NAME=/11index.php' => [
                '/11index.php',
                '', // Used to produce "/1" - but OUGHT to product nothing
            ],

            // Edge case: same pattern in subdir (also off-by-one)
            'subdir with 11index.php' => [
                '/mysite/11index.php',
                '', // Used to produce "/mysite/1"
            ],

            // Truncated dispatcher (no match → false → treated as 0, yields empty)
            'truncated dispatcher /index.ph' => [
                '/index.ph',
                '',
            ],

            'truncated dispatcher /index.ph' => [
                '/foo/baz/index.ph',
                '',
            ],

            'missing leading slash index.php' => [
                'index.php',
                '',
            ],

            'double leading slash index.php' => [
                '//index.php',
                '',
            ],

            'missing leading slash index.php with path' => [
                'foo/bar/index.php',
                '/foo/bar',
            ],

            'double leading slash index.php with path' => [
                '//foo/bar/index.php',
                '/foo/bar',
            ],

            'double slash within path index.php' => [
                '/foo//bar/index.php',
                '/foo/bar',
            ],

            'double slash within path' => [
                '/foo//bar/index.php',
                '/foo/bar',
            ],

            'multiple slash within path' => [
                '/foo/////bar/index.php',
                '/foo/bar',
            ],

            // Deeper normal path (sanity)
            'deep path index.php' => [
                '/deep/site/path/index.php',
                '/deep/site/path',
            ],

            'deep path index.php with periods' => [
                '/deep/some.site.com/path/index.php',
                '/deep/some.site.com/path',
            ],
        ];
    }
}

