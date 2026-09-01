<?php

namespace Concrete\Tests\Backup;

use Concrete\Core\Backup\ContentExporter;
use Concrete\Core\Backup\ContentExporterOptions;
use Concrete\Core\Backup\ContentImporter\ValueInspector\ValueInspectorInterface;
use Concrete\Core\Http\Request;
use Concrete\Core\File\Import\FileImporter;
use Concrete\TestHelpers\File\FileStorageTestCase;

class ContentImporterValueInspectorTest extends FileStorageTestCase
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'PermissionAccessEntityTypes',
        ]);
    }

    public function testMake()
    {
        $inspector = app('import/value_inspector/core');
        $this->assertInstanceOf(ValueInspectorInterface::class, $inspector);
    }

    public function testRegister()
    {
        $inspector = app('import/value_inspector/core');
        $inspector->registerInspectionRoutine(new \Concrete\Core\Backup\ContentImporter\ValueInspector\InspectionRoutine\PageRoutine());
        $this->assertEquals(1, count($inspector->getInspectionRoutines()));
    }

    public function testMakeCore()
    {
        $inspector = app('import/value_inspector');
        $inspector2 = app(ValueInspectorInterface::class);
        $this->assertSame($inspector, $inspector2);
        $this->assertEquals(7, count($inspector->getInspectionRoutines()));
    }

    public static function providerMatchedSimpleValues()
    {
        return [
            ['{ccm:export:page:/ok/here/we-go}', '/ok/here/we-go', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem'],
            ['{ccm:export:file:house.jpg}', 'house.jpg', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem'],
            ['{ccm:export:pagetype:blog}', 'blog', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageTypeItem'],
            ['{ccm:export:pagefeed:rss}', 'rss', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem'],
            ['{ccm:export:image:my_cool_pic.jpg}', 'my_cool_pic.jpg', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ImageItem'],
            ['<concrete-picture file="avatar.jpg"></concrete-picture>', 'avatar.jpg', '\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem'],
        ];
    }

    /**
     * @dataProvider providerMatchedSimpleValues
     *
     * @param mixed $content
     * @param mixed $reference
     * @param mixed $itemClass
     */
    public function testMatchedSimpleValues($content, $reference, $itemClass)
    {
        $inspector = app('import/value_inspector');
        $result = $inspector->inspect($content, false);
        $items = $result->getMatchedItems();
        $this->assertEquals(1, count($items));
        $item = $items[0];
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface', $item);
        $this->assertEquals($reference, $item->getReference());
        $this->assertInstanceOf($itemClass, $item);
    }

    public function testMatchedContentPageAndImage()
    {
        $content = <<<EOL
        <p>This is a content block. It is amazing. <a href="{ccm:export:page:/path/to/page}">Link 1</a>.
        Don't forget a second <a href="{ccm:export:page:/about}">link.</a>. Also, we're going to embed a picture
        here too. <concrete-picture alt="cats are cool"  file="cats.jpg">. It's a pretty good one. <a href="thumbs_up.html">Thumbs up!</a>

        Excellent! <a href="{ccm:export:page:/}">See you later!</a>
EOL;

        $inspector = app('import/value_inspector');
        $result = $inspector->inspect($content);
        $items = $result->getMatchedItems();
        $this->assertEquals(4, count($items));
        $this->assertEquals($items[0]->getReference(), '/path/to/page');
        $this->assertEquals($items[1]->getReference(), '/about');
        $this->assertEquals($items[2]->getReference(), '/');
        $this->assertEquals($items[3]->getReference(), 'cats.jpg');
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem', $items[0]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem', $items[1]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageItem', $items[2]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PictureItem', $items[3]);
    }

    public function testMatchedContentFilePageTypePageFeed()
    {
        $content = <<<EOL
        <p>Here is a link to an <a href="{ccm:export:pagefeed:blog}">rss feed</a>. We're also linking to a
        <a href="{ccm:export:file:filename1.jpg}">couple</a> of <A href="{ccm:export:file:filename2.JPG}">files.</a>.
        Finally, we're also going to link to a pagetype here: {ccm:export:pagetype:blog_entry}.
EOL;

        $inspector = app('import/value_inspector');
        $result = $inspector->inspect($content);
        $items = $result->getMatchedItems();
        $this->assertEquals(4, count($items));
        $this->assertEquals($items[0]->getReference(), 'filename1.jpg');
        $this->assertEquals($items[1]->getReference(), 'filename2.JPG');
        $this->assertEquals($items[2]->getReference(), 'blog');
        $this->assertEquals($items[3]->getReference(), 'blog_entry');
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem', $items[0]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\FileItem', $items[1]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageFeedItem', $items[2]);
        $this->assertInstanceOf('\Concrete\Core\Backup\ContentImporter\ValueInspector\Item\PageTypeItem', $items[3]);
    }

    public function testReplacedContent()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();


        $importer = app(FileImporter::class);
        $prefix = $importer->generatePrefix();
        \Concrete\Core\File\File::add('test.jpg', $prefix);

        $content = <<<EOL
        <p><concrete-picture alt="Lorem ipsum" file="test.jpg">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip <concrete-picture file="test.jpg" alt="ex ea commodo consequat." width="200" height="100" style="border: 1px solid black;" /></p>
EOL;

        $expected = <<<EOL
        <p><concrete-picture fID="1" alt="Lorem ipsum" />Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip <concrete-picture fID="1" alt="ex ea commodo consequat." width="200" height="100" style="border: 1px solid black;" /></p>
EOL;

        $inspector = app('import/value_inspector');
        $result = $inspector->inspect($content);

        $this->assertEquals($expected, $result->getReplacedContent());
    }

    /**
     * Files imported from a CIF package may get a brand new prefix, so references without a prefix
     * must be resolved too.
     *
     * @see \Concrete\Core\Backup\ContentExporterOptions::setExportFilesWithoutPrefix()
     */
    public function testReplacedContentWithoutPrefix()
    {
        $file = $this->createFile('without-prefix.jpg');

        $content = <<<EOL
        <a href="{ccm:export:file:without-prefix.jpg}">download</a> <concrete-picture file="without-prefix.jpg" alt="Lorem ipsum" />
EOL;

        $expected = <<<EOL
        <a href="{CCM:FID_DL_{$this->getFileReference($file)}}">download</a> <concrete-picture fID="{$file->getFileID()}" alt="Lorem ipsum" />
EOL;

        $inspector = app('import/value_inspector');
        $result = $inspector->inspect($content);

        $this->assertEquals($expected, $result->getReplacedContent());
    }

    public function testReplacedContentWithIDs()
    {
        $file = $this->createFile('with-ids.jpg');
        $fID = $file->getFileID();

        $content = <<<EOL
        <a href="{ccm:export:file:id={$fID}}">download</a> <concrete-picture file-id="{$fID}" alt="Lorem ipsum" />
EOL;

        $expected = <<<EOL
        <a href="{CCM:FID_DL_{$this->getFileReference($file)}}">download</a> <concrete-picture fID="{$fID}" alt="Lorem ipsum" />
EOL;

        $inspector = app('import/value_inspector');
        $result = $inspector->inspect($content);

        $this->assertEquals($expected, $result->getReplacedContent());
    }

    public function testReplacedContentWithUUIDs()
    {
        $file = $this->createFile('with-uuid.jpg');
        $uuid = $file->getFileUUID();
        $this->assertNotEmpty($uuid, 'newly added files should have a UUID');

        $content = <<<EOL
        <a href="{ccm:export:file:id={$uuid}}">download</a> <concrete-picture file-id="{$uuid}" alt="Lorem ipsum" />
EOL;

        $expected = <<<EOL
        <a href="{CCM:FID_DL_{$uuid}}">download</a> <concrete-picture fID="{$file->getFileID()}" alt="Lorem ipsum" />
EOL;

        $inspector = app('import/value_inspector');
        $result = $inspector->inspect($content);

        $this->assertEquals($expected, $result->getReplacedContent());
    }

    public function testFileReferencesAreExportedAsUUID()
    {
        $file = $this->createFile('exported.jpg');
        $originalOptions = ContentExporter::getOptions();
        try {
            // an API request: IDs are exported, and file references use UUIDs
            ContentExporter::setOptions(new ContentExporterOptions(Request::create('/ccm/api/1.0/pages/1')));
            $this->assertSame(
                "{ccm:export:file:id={$file->getFileUUID()}}",
                ContentExporter::replaceFileWithPlaceHolder($file->getFileID())
            );
            // the very same file, exported by ID
            ContentExporter::getOptions()->setExportFilesAsUUID(false);
            $this->assertSame(
                "{ccm:export:file:id={$file->getFileID()}}",
                ContentExporter::replaceFileWithPlaceHolder($file->getFileID())
            );
        } finally {
            ContentExporter::setOptions($originalOptions);
        }
    }

    /**
     * References that can't be resolved are dropped from the content (this has always been the case:
     * the routines replace them with the empty string when the item has no content value).
     */
    public function testUnresolvedReferencesAreDropped()
    {
        $this->createFile('unresolved.jpg');

        $content = 'A<concrete-picture file="not-there.jpg" alt="Lorem ipsum" />B{ccm:export:file:id=12345}C';

        $inspector = app('import/value_inspector');
        $result = $inspector->inspect($content);

        $this->assertEquals('ABC', $result->getReplacedContent());
    }

    /**
     * Create the default storage location and add a file to it.
     *
     * @param string $filename
     *
     * @return \Concrete\Core\Entity\File\File
     */
    private function createFile($filename)
    {
        if (!is_dir($this->getStorageDirectory())) {
            mkdir($this->getStorageDirectory());
        }
        $this->getStorageLocation();

        $importer = app(FileImporter::class);
        $version = \Concrete\Core\File\File::add($filename, $importer->generatePrefix());

        return $version->getFile();
    }

    /**
     * Get the value used by FileItem to reference a file.
     *
     * @param \Concrete\Core\Entity\File\File $file
     *
     * @return string
     */
    private function getFileReference($file)
    {
        return (string) ($file->getFileUUID() ?: $file->getFileID());
    }
}
