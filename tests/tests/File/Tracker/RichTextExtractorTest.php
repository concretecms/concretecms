<?php

declare(strict_types=1);

namespace Concrete\Tests\File\Tracker;

use Concrete\Core\File\Tracker\RichTextExtractor;
use Concrete\Tests\TestCase;

class RichTextExtractorTest extends TestCase
{
    /**
     * @var \Concrete\Core\File\Tracker\RichTextExtractor
     */
    private static $extractor;

    public static function setUpBeforeClass():void
    {
        self::$extractor = new RichTextExtractor();
    }

    public function provideExtractFilesCases(): array
    {
        return [
            [
                null,
                [],
            ],
            [
                '',
                [],
            ],
            [
                'foo',
                [],
            ],
            [
                '<concrete-picture fID="123" />',
                [123],
            ],
            [
                '<concrete-picture fID="0" />',
                [],
                'Invalid ID (zero)'
            ],
            [
                '<concrete-picture fID="-10" />',
                [],
                'Invalid ID (negative)'
            ],
            [
                '<concrete-picture fID="' . str_repeat('9', 20) . '" />',
                [],
                'Invalid ID (too long)'
            ],
            [
                '<concrete-picture fID="0123" />',
                [],
                'Invalid ID (starts with zero)'
            ],
            [
                '<concrete-picture fid="12345678-ba33-47ba-9fc6-f7948e8f509a" />',
                ['12345678-ba33-47ba-9fc6-f7948e8f509a'],
            ],
            [
                '<CONCRETE-PICTURE FID="12345678-BA33-47BA-9FC6-F7948E8F509A" />',
                ['12345678-ba33-47ba-9fc6-f7948e8f509a'],
            ],
            [
                '<concrete-picture fID="12345678-ba33-47ba-9fc6-f7948e8f509Z" />',
                [],
                'UUID-like but with invalid char (Z)',
            ],
            [
                '<concrete-picture fid="12345678-ba33-47ba-9fc6-f7948e8f509" />',
                [],
                'UUID-like but with shorter length',
            ],
            [
                '<concrete-picture fid="12345678-ba33-47ba-9fc6-f7948e8f509aa" />',
                [],
                'UUID-like but with longer length',
            ],
            [
                '<a href="{CCM:FID_DL_123}">Test</a>',
                [123],
            ],
            [
                '<a href="{CCM:FID_DL_12345678-ba33-47ba-9fc6-F7948E8F509A}">Test</a>',
                ['12345678-ba33-47ba-9fc6-f7948e8f509a'],
            ],
            [
                '<a href="{CCM:FID_DL_12345678-ba33-47ba-9fc6-F7948E8F509A}"><concrete-picture fID="123" />Test</a>',
                [123, '12345678-ba33-47ba-9fc6-f7948e8f509a'],
            ],
            [
                <<<'EOT'
                <a href="{CCM:FID_DL_12345678-ba33-47ba-9fc6-F7948E8F509A}"><concrete-picture fID="123" />Test 1</a>
                <a href="{CCM:FID_DL_1}"><concrete-picture fID="0b7c5173-d506-4844-8130-f88ead885426" />Test 1</a>
                EOT,
                ['0b7c5173-d506-4844-8130-f88ead885426', 123, '12345678-ba33-47ba-9fc6-f7948e8f509a', 1],
            ],
        ];
    }

    /**
     * @dataProvider provideExtractFilesCases
     *
     * @param string|mixed $richText
     * @param array $expectedValues
     */
    public function testExtractFiles($richText, array $expectedValues, string $message = ''): void
    {
        $actualValues = self::$extractor->extractFiles($richText);
        $this->assertSame($expectedValues, $actualValues, $message);
    }
}
