<?php

namespace Concrete\Tests\Validation;

use Concrete\Core\Validation\SanitizeService;
use Concrete\Tests\TestCase;
use Concrete\TestHelpers\Validation\StringableClass;
use Concrete\TestHelpers\Validation\NonStringableClass;

/**
 * FILTER_SANITIZE_STRING is deprecated in PHP 8.1, so we re
 */
class SanitizeTest extends TestCase
{
    /**
     * @var \Concrete\Core\Validation\SanitizeService
     */
    private static $service;

    public static function setupBeforeClass(): void
    {
        self::$service = new SanitizeService();
    }

    /**
     * @dataProvider provideSanitizeStringCases
     */
    public function testSanitizeString($value)
    {
        if (!defined('FILTER_SANITIZE_STRING')) {
            self::markTestSkipped('FILTER_SANITIZE_STRING is no more available (it was deprecated since PHP 8.1');
        }
        $errorReporting = error_reporting();
        error_reporting($errorReporting & ~E_DEPRECATED);
        try {
            // This is the old implementation of our sanitizeString
            $expected = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        } finally {
            error_reporting($errorReporting);
        }
        $actual = self::$service->sanitizeString($value);

        $this->assertSame($expected, $actual);
    }

    public function provideSanitizeStringCases(): array
    {
        return [
            ['plain text'],
            ["plain text"],
            [false],
            [true],
            [null],
            [new StringableClass()],
            [new NonStringableClass()],
            [[]],
            [''],
            ['Hello world'],
            [1],
            [1.2],
            ['Hello <script>world</script>'],
            ['Hello <script>world'],
            ['Hello <script world'],
            ["Hello <script\nworld"],
            ["Hello < script\nworld"],
            ["Hello <script\n>world</\nscri\npt>"],
            ['`x` > "y"'],
            ["Hèllo"],
            ['H`&llo`'],
        ];
    }
}
