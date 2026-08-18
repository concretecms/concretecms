<?php

declare(strict_types=1);

namespace Concrete\Tests\Backup;

use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\FileFolderRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\FileRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\ImageRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageFeedRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageTypeRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PictureRoutine;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ImageItem;
use Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the parsing performed by the inspection routines (no database access involved).
 */
class ContentImporterInspectionRoutineTest extends TestCase
{
    public static function providerFileReferences(): array
    {
        return [
            'prefix and file name' => ['123456789012:atomik-logo.png', '123456789012', 'atomik-logo.png', ''],
            'file name only' => ['atomik-logo.png', null, 'atomik-logo.png', ''],
            'ID only' => [':id=123', null, '', 123],
            'UUID only' => [':id=1d0e5f9c-4e4c-4bd6-96b0-6ff8a6b2ea55', null, '', '1d0e5f9c-4e4c-4bd6-96b0-6ff8a6b2ea55'],
            'uppercase UUID' => [':id=E3F7CE45-CF48-43EA-B7E0-EEA71E1E773A', null, '', 'E3F7CE45-CF48-43EA-B7E0-EEA71E1E773A'],
            'file name looking like a UUID reference' => ['logo:id=x.png', 'logo', 'id=x.png', ''],
            'ID with leading zeroes' => [':id=007', '', 'id=007', ''],
            'truncated UUID' => [':id=e3f7ce45-cf48-43ea-b7e0', '', 'id=e3f7ce45-cf48-43ea-b7e0', ''],
            'file name containing an equal sign' => ['1234:a=b.png', '1234', 'a=b.png', ''],
            'file name containing a colon' => ['1234:a:b.png', '1234', 'a:b.png', ''],
            'file name looking like an ID' => ['id=123', null, 'id=123', ''],
            'empty reference' => ['', null, '', ''],
        ];
    }

    /**
     * @dataProvider providerFileReferences
     */
    public function testFileRoutine(string $reference, ?string $prefix, string $filename, $fileID): void
    {
        $items = (new FileRoutine())->match("{ccm:export:file:{$reference}}");

        static::assertCount(1, $items);
        $item = $items[0];
        static::assertInstanceOf(FileItem::class, $item);
        static::assertSame($prefix, $item->getPrefix());
        static::assertSame($filename, $item->getFilename());
        static::assertSame($fileID, $item->getFileID());
    }

    /**
     * @dataProvider providerFileReferences
     */
    public function testImageRoutine(string $reference, ?string $prefix, string $filename, $fileID): void
    {
        $items = (new ImageRoutine())->match("{ccm:export:image:{$reference}}");

        static::assertCount(1, $items);
        $item = $items[0];
        static::assertInstanceOf(ImageItem::class, $item);
        static::assertSame($prefix, $item->getPrefix());
        static::assertSame($filename, $item->getFilename());
        static::assertSame($fileID, $item->getFileID());
    }

    public static function providerSimpleReferences(): array
    {
        return [
            'page by path' => [PageRoutine::class, '{ccm:export:page:/ok/here/we-go}', '/ok/here/we-go'],
            'page by ID' => [PageRoutine::class, '{ccm:export:page::id=123}', ':id=123'],
            'home page' => [PageRoutine::class, '{ccm:export:page:}', ''],
            'page type by handle' => [PageTypeRoutine::class, '{ccm:export:pagetype:blog}', 'blog'],
            'page type by ID' => [PageTypeRoutine::class, '{ccm:export:pagetype::id=5}', ':id=5'],
            'page feed by handle' => [PageFeedRoutine::class, '{ccm:export:pagefeed:rss}', 'rss'],
            'page feed by ID' => [PageFeedRoutine::class, '{ccm:export:pagefeed::id=5}', ':id=5'],
            'file folder by path' => [FileFolderRoutine::class, '{ccm:export:filefolder:/My Folder}', '/My Folder'],
            'file folder by ID' => [FileFolderRoutine::class, '{ccm:export:filefolder::id=5}', ':id=5'],
        ];
    }

    /**
     * @dataProvider providerSimpleReferences
     */
    public function testSimpleReferences(string $routineClass, string $content, string $expectedReference): void
    {
        $items = (new $routineClass())->match($content);

        static::assertCount(1, $items);
        static::assertSame($expectedReference, $items[0]->getReference());
    }

    public static function providerPictureElements(): array
    {
        return [
            'prefix and file name' => [
                '<concrete-picture file="1234:test.png" alt="x" />',
                '1234', 'test.png', '', 'alt="x"',
            ],
            'file name only' => [
                '<concrete-picture file="test.png">',
                null, 'test.png', '', '',
            ],
            'file ID' => [
                '<concrete-picture file-id="7">',
                null, '', 7, '',
            ],
            'file UUID' => [
                '<concrete-picture file-id="1d0e5f9c-4e4c-4bd6-96b0-6ff8a6b2ea55" alt="x" />',
                null, '', '1d0e5f9c-4e4c-4bd6-96b0-6ff8a6b2ea55', 'alt="x"',
            ],
            'uppercase file UUID' => [
                '<concrete-picture file-id="E3F7CE45-CF48-43EA-B7E0-EEA71E1E773A">',
                null, '', 'E3F7CE45-CF48-43EA-B7E0-EEA71E1E773A', '',
            ],
            'truncated file UUID' => [
                '<concrete-picture file-id="e3f7ce45-cf48-43ea-b7e0">',
                null, '', '', '',
            ],
            'neither an ID nor a UUID' => [
                '<concrete-picture file-id="not-an-identifier" alt="x" />',
                null, '', '', 'alt="x"',
            ],
            'file ID with other attributes' => [
                '<concrete-picture file-id="123" class="a b" />',
                null, '', 123, 'class="a b"',
            ],
            'uppercase attribute, single quotes, similarly named attribute' => [
                '<concrete-picture FILE-ID=\'9\' data-file="nope">',
                null, '', 9, 'data-file="nope"',
            ],
            'file name wins over file ID' => [
                '<concrete-picture file-id="7" file="abc.png">',
                null, 'abc.png', '', '',
            ],
            'empty file name falls back to the file ID' => [
                '<concrete-picture file="" file-id="5">',
                null, '', 5, '',
            ],
            'greater-than sign within an attribute value' => [
                '<concrete-picture file="x.png" alt="a > b" />',
                null, 'x.png', '', 'alt="a > b"',
            ],
            'greater-than sign before the file attribute' => [
                '<concrete-picture alt="a > b" file="x.png" />',
                null, 'x.png', '', 'alt="a > b"',
            ],
            'greater-than sign within single-quoted attribute values' => [
                '<concrete-picture file="x.png" alt=\'1 > 0\' title="q>r" />',
                null, 'x.png', '', 'alt=\'1 > 0\' title="q>r"',
            ],
            'attributes spanning multiple lines' => [
                "<concrete-picture\n  file=\"x.png\"\n  alt=\"y\"\n/>",
                null, 'x.png', '', 'alt="y"',
            ],
            'unquoted and boolean attributes' => [
                '<concrete-picture file="x.png" width=200 hidden>',
                null, 'x.png', '', 'width=200 hidden',
            ],
            'file name containing colons' => [
                '<concrete-picture file="a:b:c.png">',
                'a', 'b:c.png', '', '',
            ],
        ];
    }

    /**
     * @dataProvider providerPictureElements
     */
    public function testPictureRoutine(string $element, ?string $prefix, string $filename, $fileID, string $additionalAttributes): void
    {
        $items = (new PictureRoutine())->match($element);

        static::assertCount(1, $items);
        $item = $items[0];
        static::assertInstanceOf(PictureItem::class, $item);
        static::assertSame($prefix, $item->getPrefix());
        static::assertSame($filename, $item->getFilename());
        static::assertSame($fileID, $item->getFileID());
        static::assertSame($additionalAttributes, $item->getAdditionalAttributes());
    }

    public static function providerNotPictureElements(): array
    {
        return [
            'another element' => ['<concrete-pictures file="x.png">'],
            'element with a longer name' => ['<concrete-picture-foo file="x.png">'],
            'no file attribute at all' => ['<concrete-picture alt="x">'],
            'similarly named attributes only' => ['<concrete-picture data-file="x.png" filename="y.png">'],
            'unbalanced quotes' => ['<concrete-picture alt="unclosed file="x.png">'],
        ];
    }

    /**
     * @dataProvider providerNotPictureElements
     */
    public function testPictureRoutineNotMatching(string $content): void
    {
        static::assertSame([], (new PictureRoutine())->match($content));
    }

    public function testPictureRoutineMatchesEveryElement(): void
    {
        $content = <<<'EOT'
                <p><concrete-picture file="a.png"></p>
                <p><concrete-picture file="1234:b.png" alt="k > j"></p>
                <p><concrete-picture file-id="3" /></p>
        EOT;

        $items = (new PictureRoutine())->match($content);

        static::assertCount(3, $items);
        static::assertSame('a.png', $items[0]->getFilename());
        static::assertSame('', $items[0]->getAdditionalAttributes());
        static::assertSame('1234', $items[1]->getPrefix());
        static::assertSame('b.png', $items[1]->getFilename());
        static::assertSame('alt="k > j"', $items[1]->getAdditionalAttributes());
        static::assertSame(3, $items[2]->getFileID());
    }
}
