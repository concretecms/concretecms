<?php

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

/**
 * Tests the parsing performed by the inspection routines (no database access involved).
 */
class ContentImporterInspectionRoutineTest extends TestCase
{
    public static function providerFileReferences(): array
    {
        return [
            'prefix and file name' => ['123456789012:atomik-logo.png', '123456789012', 'atomik-logo.png', null],
            'file name only' => ['atomik-logo.png', null, 'atomik-logo.png', null],
            'ID only' => [':id=123', null, '', 123],
            'file name containing an equal sign' => ['1234:a=b.png', '1234', 'a=b.png', null],
            'file name containing a colon' => ['1234:a:b.png', '1234', 'a:b.png', null],
            'file name looking like an ID' => ['id=123', null, 'id=123', null],
            'empty reference' => ['', null, '', null],
        ];
    }

    /**
     * @dataProvider providerFileReferences
     */
    public function testFileRoutine(string $reference, ?string $prefix, string $filename, ?int $fileID): void
    {
        $items = (new FileRoutine())->match("{ccm:export:file:{$reference}}");

        $this->assertCount(1, $items);
        $item = $items[0];
        $this->assertInstanceOf(FileItem::class, $item);
        $this->assertSame($prefix, $item->getPrefix());
        $this->assertSame($filename, $item->getFilename());
        $this->assertSame($fileID, $item->getFileID());
    }

    /**
     * @dataProvider providerFileReferences
     */
    public function testImageRoutine(string $reference, ?string $prefix, string $filename, ?int $fileID): void
    {
        $items = (new ImageRoutine())->match("{ccm:export:image:{$reference}}");

        $this->assertCount(1, $items);
        $item = $items[0];
        $this->assertInstanceOf(ImageItem::class, $item);
        $this->assertSame($prefix, $item->getPrefix());
        $this->assertSame($filename, $item->getFilename());
        $this->assertSame($fileID, $item->getFileID());
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

        $this->assertCount(1, $items);
        $this->assertSame($expectedReference, $items[0]->getReference());
    }

    public static function providerPictureElements(): array
    {
        return [
            'prefix and file name' => [
                '<concrete-picture file="1234:test.png" alt="x" />',
                '1234', 'test.png', null, 'alt="x"',
            ],
            'file name only' => [
                '<concrete-picture file="test.png">',
                null, 'test.png', null, '',
            ],
            'file ID' => [
                '<concrete-picture file-id="7">',
                null, '', 7, '',
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
                null, 'abc.png', null, '',
            ],
            'empty file name falls back to the file ID' => [
                '<concrete-picture file="" file-id="5">',
                null, '', 5, '',
            ],
            'greater-than sign within an attribute value' => [
                '<concrete-picture file="x.png" alt="a > b" />',
                null, 'x.png', null, 'alt="a > b"',
            ],
            'greater-than sign before the file attribute' => [
                '<concrete-picture alt="a > b" file="x.png" />',
                null, 'x.png', null, 'alt="a > b"',
            ],
            'greater-than sign within single-quoted attribute values' => [
                '<concrete-picture file="x.png" alt=\'1 > 0\' title="q>r" />',
                null, 'x.png', null, 'alt=\'1 > 0\' title="q>r"',
            ],
            'attributes spanning multiple lines' => [
                "<concrete-picture\n  file=\"x.png\"\n  alt=\"y\"\n/>",
                null, 'x.png', null, 'alt="y"',
            ],
            'unquoted and boolean attributes' => [
                '<concrete-picture file="x.png" width=200 hidden>',
                null, 'x.png', null, 'width=200 hidden',
            ],
            'file name containing colons' => [
                '<concrete-picture file="a:b:c.png">',
                'a', 'b:c.png', null, '',
            ],
        ];
    }

    /**
     * @dataProvider providerPictureElements
     */
    public function testPictureRoutine(string $element, ?string $prefix, string $filename, ?int $fileID, string $additionalAttributes): void
    {
        $items = (new PictureRoutine())->match($element);

        $this->assertCount(1, $items);
        $item = $items[0];
        $this->assertInstanceOf(PictureItem::class, $item);
        $this->assertSame($prefix, $item->getPrefix());
        $this->assertSame($filename, $item->getFilename());
        $this->assertSame($fileID, $item->getFileID());
        $this->assertSame($additionalAttributes, $item->getAdditionalAttributes());
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
        $this->assertSame([], (new PictureRoutine())->match($content));
    }

    public function testPictureRoutineMatchesEveryElement(): void
    {
        $content = <<<'EOT'
        <p><concrete-picture file="a.png"></p>
        <p><concrete-picture file="1234:b.png" alt="k > j"></p>
        <p><concrete-picture file-id="3" /></p>
EOT;

        $items = (new PictureRoutine())->match($content);

        $this->assertCount(3, $items);
        $this->assertSame('a.png', $items[0]->getFilename());
        $this->assertSame('', $items[0]->getAdditionalAttributes());
        $this->assertSame('1234', $items[1]->getPrefix());
        $this->assertSame('b.png', $items[1]->getFilename());
        $this->assertSame('alt="k > j"', $items[1]->getAdditionalAttributes());
        $this->assertSame(3, $items[2]->getFileID());
    }
}
