<?php

namespace Concrete\Tests\File\Import;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\File\Importer;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\File\FileStorageTestCase;
use Config;
use Concrete\Core\File\Import\ImportOptions;

class FileImporterTest extends FileStorageTestCase
{
    /**
     * @var \Concrete\Core\Application\Application;
     */
    protected static $app;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
            'Users',
            'PermissionAccessEntityTypes',
            'FileImageThumbnailTypes',
            'FileImageThumbnailPaths',
            'FilePermissionAssignments',
            'ConfigStore',
            'Logs',
            'FileVersionLog',
        ]);
        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Attribute\Key\Settings\NumberSettings',
            'Concrete\Core\Entity\Attribute\Key\Settings\Settings',
            'Concrete\Core\Entity\Attribute\Key\Settings\EmptySettings',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
        ]);
        Config::set('concrete.upload.extensions', '*.txt;*.jpg;*.jpeg;*.png');
    }

    public function setUp(): void
    {
        parent::setUp();
        $config = static::$app->make('config');
        $config->set('concrete.misc.default_thumbnail_format', 'jpeg');
        $config->set('concrete.misc.basic_thumbnailer_generation_strategy', 'now');
    }

    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();
        self::$app = Application::getFacadeApplication();
        Category::add('file');
        $number = AttributeType::add('number', 'Number');
        FileKey::add($number, ['akHandle' => 'width', 'akName' => 'Width']);
        FileKey::add($number, ['akHandle' => 'height', 'akName' => 'Height']);

        CacheLocal::flush();
    }

    public function testFileNotFound()
    {
        $this->expectException(\Concrete\Core\File\Import\ImportException::class);
        $fi = static::$app->make(FileImporter::class);
        $fi->importLocalFile('foo.txt', 'foo.txt');
    }

    public function testFileInvalidExtension()
    {
        $this->expectException(\Concrete\Core\File\Import\ImportException::class);
        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.invalid';
        touch($file);
        $fi = static::$app->make(FileImporter::class);
        $fi->importLocalFile($file, 'test.invalid');
    }

    public function testFileInvalidStorageLocation()
    {
        $this->expectException(\Concrete\Core\File\Import\ImportException::class);
        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.txt';
        touch($file);
        $fi = static::$app->make(FileImporter::class);
        $fi->importLocalFile($file, 'test.txt');
    }

    public function testFileValid()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.txt';
        touch($file);
        $fi = static::$app->make(FileImporter::class);
        $r = $fi->importLocalFile($file, 'test.txt');

        static::assertInstanceOf('\Concrete\Core\Entity\File\Version', $r);
        static::assertEquals($r->getFileVersionID(), 1);
        static::assertEquals($r->getFileID(), 1);
        static::assertEquals('test.txt', $r->getFilename());
        $fo = $r->getFile();
        $fsl = $fo->getFileStorageLocationObject();
        static::assertEquals(true, $fsl->isDefault());
        static::assertInstanceOf('\Concrete\Core\Entity\File\StorageLocation\StorageLocation', $fsl);
        $apr = str_split($r->getPrefix(), 4);

        static::assertEquals('/application/files/' . $apr[0] . '/' . $apr[1] . '/' . $apr[2] . '/test.txt',
            $r->getRelativePath()
        );
    }

    public function testFileVersions()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.txt';
        touch($file);
        $fi = static::$app->make(FileImporter::class);
        $fi->importLocalFile($file, 'test.txt');

        $f = \File::getByID(1);
        $versions = $f->getFileVersions();
        static::assertEquals(1, count($versions));
    }

    public function testImageImportSize()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = DIR_BASE . '/concrete/images/logo.png';
        $fi = static::$app->make(FileImporter::class);
        $fo = $fi->importLocalFile($file, 'My Logo.png');
        $type = $fo->getTypeObject();
        static::assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

        static::assertEquals(113, $fo->getAttribute('width'));
        static::assertEquals(113, $fo->getAttribute('height'));
    }

    public function testThumbnailStorageLocation()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $helper = static::$app->make('helper/concrete/file');
        $path = $helper->getThumbnailFilePath('137803870092', 'testing.gif', 1);
        static::assertEquals('/thumbnails/1378/0387/0092/testing.jpg', $path);
    }

    public function testImageImport()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $cf = static::$app->make('helper/concrete/file');
        $fh = static::$app->make('helper/file');
        $config = static::$app->make('config');
        $file = DIR_BASE . '/concrete/config/install/packages/elemental_full/files/123412345678_plants.jpg';
        $humbnailTypes = ThumbnailType::getList();
        foreach ([
            'auto' => ['jpg', IMAGETYPE_JPEG],
            'jpeg' => ['jpg', IMAGETYPE_JPEG],
            'png' => ['png', IMAGETYPE_PNG],
        ] as $thumbnailFormat => list($expectedExtension, $expectedFileType)) {
            $config->set('concrete.misc.default_thumbnail_format', $thumbnailFormat);
            foreach (['now'] as $strategy) {
                $config->set('concrete.misc.basic_thumbnailer_generation_strategy', $strategy);
                $fi = static::$app->make(FileImporter::class);
                $fo = $fi->importLocalFile($file, '123412345678_plants.jpg');
                static::assertTrue(is_object($fo), 'Import failed (' . (is_object($fo) ? null : Importer::getErrorMessage($fo)) . ')');
                $type = $fo->getTypeObject();
                static::assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

                static::assertTrue((bool) $fo->hasThumbnail(1));
                static::assertTrue((bool) $fo->hasThumbnail(2));
                static::assertFalse((bool) $fo->hasThumbnail(3));

                static::assertEquals(
                    'http://www.dummyco.com/application/files/thumbnails/file_manager_detail' . $cf->prefix($fo->getPrefix(), $fh->replaceExtension($fo->getFilename(), $expectedExtension), 2),
                    $fo->getThumbnailURL('file_manager_detail'),
                    "Check thumbnail URL with: format={$thumbnailFormat}, strategy={$strategy}"
                );

                $storageLocation = $fo->getFile()->getFileStorageLocationObject();
                /* @var \Concrete\Core\Entity\File\StorageLocation\StorageLocation $storageLocation */
                $fsl = $storageLocation->getFileSystemObject();
                /* @var \League\Flysystem\Filesystem $fsl */
                foreach ($humbnailTypes as $thumbnailType) {
                    foreach ([
                        $thumbnailType->getBaseVersion(),
                        $thumbnailType->getDoubledVersion(),
                    ] as $thumbnailTypeVersion) {
                        $thumbnailPath = $thumbnailTypeVersion->getFilePath($fo);
                        static::assertTrue($fsl->has($thumbnailPath), "Check thumbnail existence with: format={$thumbnailFormat}, strategy={$strategy}");
                        $handler = $fsl->get($thumbnailPath);
                        list($width, $height, $type) = getimagesizefromstring($handler->read());
                        static::assertSame($expectedFileType, $type, "Check thumbnail type with: format={$thumbnailFormat}, strategy={$strategy}");
                    }
                }
                $basicThumbnailer = static::$app->make(BasicThumbnailer::class, ['storageLocation' => $storageLocation]);
                /* @var BasicThumbnailer $basicThumbnailer */
                $thumbnail = $basicThumbnailer->getThumbnail($fo->getFile(), 100, 100);
                static::assertSame('.' . $expectedExtension, substr($thumbnail->src, -4));
                static::assertStringContainsString('/application/files/cache/thumbnails/', $thumbnail->src);
                $pos = strrpos($thumbnail->src, '/cache/thumbnails/');
                $realPath = $this->getStorageDirectory() . substr($thumbnail->src, $pos);
                if ($strategy === 'now') {
                    static::assertGreaterThan(0, $thumbnail->width);
                    static::assertGreaterThan(0, $thumbnail->height);
                    static::assertLessThanOrEqual(100, $thumbnail->width);
                    static::assertLessThanOrEqual(100, $thumbnail->height);
                    static::assertFileExists($realPath);
                    list($width, $height, $type) = getimagesize($realPath);
                    static::assertSame($thumbnail->width, $width);
                    static::assertSame($thumbnail->height, $height);
                    static::assertSame($expectedFileType, $type);
                }
            }
        }
    }

    public function testImageImportFromIncoming()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $incomingPath = $this->getStorageDirectory() . '/incoming';
        mkdir($incomingPath);

        copy(DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png', $incomingPath . '/trees.png');

        $fi = static::$app->make(FileImporter::class);
        $fo = $fi->importFromIncoming('trees.png');
        static::assertInstanceOf('\Concrete\Core\Entity\File\Version', $fo);
        $type = $fo->getTypeObject();
        static::assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

        static::assertTrue((bool) $fo->hasThumbnail(1));
        static::assertTrue((bool) $fo->hasThumbnail(2));
        static::assertFalse((bool) $fo->hasThumbnail(3));

        $cf = static::$app->make('helper/concrete/file');
        $fh = static::$app->make('helper/file');
        static::assertEquals('http://www.dummyco.com/application/files/thumbnails/file_manager_detail'
            . $cf->prefix($fo->getPrefix(), $fh->replaceExtension($fo->getFilename(), 'jpg'), 2),
            $fo->getThumbnailURL('file_manager_detail'));
    }

    public function testFileVersionDelete()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $fi = static::$app->make(FileImporter::class);
        $file = $fi->importLocalFile($sample, 'sample.txt');

        $f = \File::getByID($file->getFileID());
        $fv = $f->getVersion(1);
        static::assertEquals('sample.txt', $fv->getFilename());
        $fv->delete();

        CacheLocal::flush();

        $fv2 = $f->getVersion(1);
        static::assertNull($fv2);
    }

    public function testImporterMimeType()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $fi = static::$app->make(FileImporter::class);
        $fo1 = $fi->importLocalFile($sample, 'sample.txt');

        $sample = DIR_TESTS . '/assets/File/StorageLocation/tiny.png';
        $fi = static::$app->make(FileImporter::class);
        $fo2 = $fi->importLocalFile($sample, 'tiny.png');

        static::assertEquals('text/plain', $fo1->getMimeType());
        static::assertEquals('image/png', $fo2->getMimeType());
    }

    public function testFileDuplicate()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $fi = static::$app->make(FileImporter::class);
        $file = $fi->importLocalFile($sample, 'sample.txt');

        $f = \File::getByID($file->getFileID());
        $f2 = $f->duplicate();
        static::assertNotEquals($file->getFileID(), $f2->getFileID());

        $versions = $f2->getVersionList();
        static::assertCount(1, $versions);
        static::assertEquals(1, $versions[0]->getFileVersionID());
        static::assertEquals($f2->getFileID(), $versions[0]->getFileID());
    }

    public function testFileAttributesDuplicate()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/tiny.png';
        $fi = static::$app->make(FileImporter::class);
        $f = $fi->importLocalFile($sample, 'tiny.png');

        $f2 = $f->duplicate();

        $attributes = $f->getAttributes();
        $attributesNew = $f2->getAttributes();
        static::assertCount(2, $attributes);
        static::assertCount(2, $attributesNew);
    }

    public function testFileVersionDuplicate()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $fi = static::$app->make(FileImporter::class);
        $f = $fi->importLocalFile($sample, 'sample.txt')->getFile();

        $fv = $f->getVersion(1);
        $fv2 = $fv->duplicate();
        static::assertEquals(2, $fv2->getFileVersionID());
        static::assertEquals(false, $fv->isApproved());
    }

    public function testFileReplace()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.txt';
        touch($file);
        $fi = static::$app->make(FileImporter::class);
        $fo = $fi->importLocalFile($file, 'test.txt');
        $fo = $fo->getFile();
        $importOptions = static::$app->make(ImportOptions::class)->setAddNewVersionTo($fo);

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $r = $fi->importLocalFile($sample, 'sample.txt', $importOptions);

        static::assertInstanceOf('\Concrete\Core\Entity\File\Version', $r);
        static::assertEquals(2, $r->getFileVersionID());
        static::assertEquals('sample.txt', $r->getFilename());
        $apr = str_split($r->getPrefix(), 4);
        static::assertEquals('http://www.dummyco.com/application/files/' . $apr[0] . '/' . $apr[1] . '/' . $apr[2] . '/sample.txt',
            $r->getURL()
        );
    }

    public function testVersionApprove()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.txt';
        touch($file);
        $fi = static::$app->make(FileImporter::class);
        $r = $fi->importLocalFile($file, 'test.txt');

        $fv2 = $r->duplicate();
        $fv3 = $r->duplicate();
        $fv4 = $r->duplicate();
        $f = \File::getByID($r->getFileID());
        $fv4b = $f->getVersion(4);

        static::assertEquals(1, $r->getFileVersionID());
        static::assertEquals(2, $fv2->getFileVersionID());
        static::assertEquals(3, $fv3->getFileVersionID());
        static::assertEquals(4, $fv4b->getFileVersionID());
        static::assertEquals(4, $fv4->getFileVersionID());
        static::assertEquals($fv4, $fv4b);

        $fv3->approve();
        static::assertEquals(true, $fv3->isApproved());

        $f = \File::getByID($r->getFileID());
        $fv1 = $f->getVersion(1);
        static::assertEquals(false, $fv1->isApproved());
        $fva = $f->getApprovedVersion();
        static::assertEquals($fva, $fv3);
    }

    protected function cleanup()
    {
        parent::cleanup();
        if (file_exists(__DIR__ . '/test.txt')) {
            unlink(__DIR__ . '/test.txt');
        }
        if (file_exists(__DIR__ . '/test.invalid')) {
            unlink(__DIR__ . '/test.invalid');
        }
    }
}
