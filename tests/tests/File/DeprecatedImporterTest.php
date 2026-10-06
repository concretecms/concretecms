<?php

namespace Concrete\Tests\File;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\File\File;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Concrete\Core\File\Importer;
use Concrete\Core\Support\Facade\Facade;
use Concrete\TestHelpers\File\FileStorageTestCase;

class DeprecatedImporterTest extends FileStorageTestCase
{
    /** @var Application */
    protected $app;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
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
        $this->app = Facade::getFacadeApplication();;
        $this->app->make('config')->set('concrete.upload.extensions', '*.txt;*.jpg;*.jpeg;*.png');
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->app->make('config');
        $config->set('concrete.misc.default_thumbnail_format', 'jpeg');
        $config->set('concrete.misc.basic_thumbnailer_generation_strategy', 'now');
    }

    public static function setUpBeforeClass():void
    {
        parent::setUpBeforeClass();

        $category = Category::add('file');
        $number = AttributeType::add('number', 'Number');
        FileKey::add($number, ['akHandle' => 'width', 'akName' => 'Width']);
        FileKey::add($number, ['akHandle' => 'height', 'akName' => 'Height']);

        CacheLocal::flush();
    }

    public function testFileNotFound()
    {
        $fi = new Importer();
        $r = $fi->import('foo.txt', 'foo.txt');
        self::assertEquals(Importer::E_FILE_INVALID, $r);
    }

    public function testFileInvalidExtension()
    {
        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.invalid';
        touch($file);
        $fi = new Importer();
        $r = $fi->import($file, 'test.invalid');
        self::assertEquals(Importer::E_FILE_INVALID_EXTENSION, $r);
    }

    public function testFileInvalidStorageLocation()
    {
        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.txt';
        touch($file);
        $fi = new Importer();
        $r = $fi->import($file, 'test.txt');
        self::assertEquals(Importer::E_FILE_INVALID_STORAGE_LOCATION, $r);
    }

    public function testFileValid()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.txt';
        touch($file);
        $fi = new Importer();
        $r = $fi->import($file, 'test.txt');

        self::assertInstanceOf('\Concrete\Core\Entity\File\Version', $r);
        self::assertEquals(1, $r->getFileVersionID());
        self::assertEquals(1, $r->getFileID());
        self::assertEquals('test.txt', $r->getFilename());
        $fo = $r->getFile();
        $fsl = $fo->getFileStorageLocationObject();
        self::assertEquals(true, $fsl->isDefault());
        self::assertInstanceOf('\Concrete\Core\Entity\File\StorageLocation\StorageLocation', $fsl);
        $apr = str_split($r->getPrefix(), 4);

        self::assertEquals('/application/files/' . $apr[0] . '/' . $apr[1] . '/' . $apr[2] . '/test.txt',
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
        $fi = new Importer();
        $fi->import($file, 'test.txt');

        $f = File::getByID(1);
        $versions = $f->getFileVersions();
        self::assertCount(1, $versions);
    }

    public function testImageImportSize()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = DIR_BASE . '/concrete/images/logo.png';
        $fi = new Importer();
        $fo = $fi->import($file, 'My Logo.png');
        $type = $fo->getTypeObject();
        self::assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

        self::assertEquals(113, $fo->getAttribute('width'));
        self::assertEquals(113, $fo->getAttribute('height'));
    }

    public function testThumbnailStorageLocation()
    {
        mkdir($this->getStorageDirectory());

        $helper = $this->app->make('helper/concrete/file');
        $path = $helper->getThumbnailFilePath('137803870092', 'testing.gif', 1);
        self::assertEquals('/thumbnails/1378/0387/0092/testing.jpg', $path);
    }

    public function testImageImport()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $cf = $this->app->make('helper/concrete/file');
        $fh = $this->app->make('helper/file');
        $config = $this->app->make('config');
        $file = DIR_BASE . '/concrete/config/install/packages/elemental/files/123412345678_plants.jpg';
        $humbnailTypes = ThumbnailType::getList();
        foreach ([
            'auto' => ['jpg', IMAGETYPE_JPEG],
            'jpeg' => ['jpg', IMAGETYPE_JPEG],
            'png' => ['png', IMAGETYPE_PNG],
        ] as $thumbnailFormat => list($expectedExtension, $expectedFileType)) {
            $config->set('concrete.misc.default_thumbnail_format', $thumbnailFormat);
            foreach (['now'] as $strategy) {
                $config->set('concrete.misc.basic_thumbnailer_generation_strategy', $strategy);
                $fi = new Importer();
                $fo = $fi->import($file, '123412345678_plants.jpg');
                self::assertTrue(is_object($fo), 'Import failed (' . (is_object($fo) ? null : Importer::getErrorMessage($fo)) . ')');
                $type = $fo->getTypeObject();
                self::assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

                self::assertTrue((bool) $fo->hasThumbnail(1));
                self::assertTrue((bool) $fo->hasThumbnail(2));
                self::assertFalse((bool) $fo->hasThumbnail(3));

                self::assertEquals(
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
                        self::assertTrue($fsl->has($thumbnailPath), "Check thumbnail existence with: format={$thumbnailFormat}, strategy={$strategy}");
                        $handler = $fsl->get($thumbnailPath);
                        list($width, $height, $type) = getimagesizefromstring($handler->read());
                        self::assertSame($expectedFileType, $type, "Check thumbnail type with: format={$thumbnailFormat}, strategy={$strategy}");
                    }
                }
                $basicThumbnailer = $this->app->build(BasicThumbnailer::class, ['storageLocation' => $storageLocation]);
                /* @var BasicThumbnailer $basicThumbnailer */
                $thumbnail = $basicThumbnailer->getThumbnail($fo->getFile(), 100, 100);
                static::assertSame('.' . $expectedExtension, substr($thumbnail->src, -4));
                self::assertStringContainsString('/application/files/cache/thumbnails/', $thumbnail->src);
                $pos = strrpos($thumbnail->src, '/cache/thumbnails/');
                $realPath = $this->getStorageDirectory() . substr($thumbnail->src, $pos);
                self::assertGreaterThan(0, $thumbnail->width);
                self::assertGreaterThan(0, $thumbnail->height);
                self::assertLessThanOrEqual(100, $thumbnail->width);
                self::assertLessThanOrEqual(100, $thumbnail->height);
                self::assertFileExists($realPath);
                list($width, $height, $type) = getimagesize($realPath);
                self::assertSame($thumbnail->width, $width);
                self::assertSame($thumbnail->height, $height);
                self::assertSame($expectedFileType, $type);
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

        $fi = new Importer();
        $fo = $fi->importIncomingFile('trees.png');
        self::assertInstanceOf('\Concrete\Core\Entity\File\Version', $fo);
        $type = $fo->getTypeObject();
        self::assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

        self::assertTrue((bool) $fo->hasThumbnail(1));
        self::assertTrue((bool) $fo->hasThumbnail(2));
        self::assertFalse((bool) $fo->hasThumbnail(3));

        $cf = $this->app->make('helper/concrete/file');
        $fh = $this->app->make('helper/file');
        self::assertEquals('http://www.dummyco.com/application/files/thumbnails/file_manager_detail'
            . $cf->prefix($fo->getPrefix(), $fh->replaceExtension($fo->getFilename(), 'jpg'), 2),
            $fo->getThumbnailURL('file_manager_detail'));
    }

    public function testFileVersionDelete()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $fi = new Importer();
        $file = $fi->import($sample, 'sample.txt');

        $f = \File::getByID($file->getFileID());
        $fv = $f->getVersion(1);
        self::assertEquals('sample.txt', $fv->getFilename());
        $fv->delete();

        CacheLocal::flush();

        $fv2 = $f->getVersion(1);
        self::assertNull($fv2);
    }

    public function testImporterMimeType()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $fi = new Importer();
        $fo1 = $fi->import($sample, 'sample.txt');

        $sample = DIR_TESTS . '/assets/File/StorageLocation/tiny.png';
        $fi = new Importer();
        $fo2 = $fi->import($sample, 'tiny.png');

        self::assertEquals('text/plain', $fo1->getMimeType());
        self::assertEquals('image/png', $fo2->getMimeType());
    }

    public function testFileDuplicate()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $fi = new Importer();
        $file = $fi->import($sample, 'sample.txt');

        $f = \File::getByID($file->getFileID());
        $f2 = $f->duplicate();
        self::assertNotEquals($file->getFileID(), $f2->getFileID());

        $versions = $f2->getVersionList();
        self::assertCount(1, $versions);
        self::assertEquals(1, $versions[0]->getFileVersionID());
        self::assertEquals($f2->getFileID(), $versions[0]->getFileID());
    }

    public function testFileAttributesDuplicate()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/tiny.png';
        $fi = new Importer();
        $f = $fi->import($sample, 'tiny.png');

        $f2 = $f->duplicate();

        $attributes = $f->getAttributes();
        $attributesNew = $f2->getAttributes();
        self::assertCount(2, $attributes);
        self::assertCount(2, $attributesNew);
    }

    public function testFileVersionDuplicate()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $fi = new Importer();
        $f = $fi->import($sample, 'sample.txt')->getFile();

        $fv = $f->getVersion(1);
        $fv2 = $fv->duplicate();
        self::assertEquals(2, $fv2->getFileVersionID());
        self::assertEquals(false, $fv->isApproved());
    }

    public function testFileReplace()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__) . '/test.txt';
        touch($file);
        $fi = new Importer();
        $fo = $fi->import($file, 'test.txt');
        $fo = $fo->getFile();

        $sample = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
        $r = $fi->import($sample, 'sample.txt', $fo);

        self::assertInstanceOf('\Concrete\Core\Entity\File\Version', $r);
        self::assertEquals(2, $r->getFileVersionID());
        self::assertEquals('sample.txt', $r->getFilename());
        $apr = str_split($r->getPrefix(), 4);
        self::assertEquals('http://www.dummyco.com/application/files/' . $apr[0] . '/' . $apr[1] . '/' . $apr[2] . '/sample.txt',
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
        $fi = new Importer();
        $r = $fi->import($file, 'test.txt');

        $fv2 = $r->duplicate();
        $fv3 = $r->duplicate();
        $fv4 = $r->duplicate();
        $f = \File::getByID($r->getFileID());
        $fv4b = $f->getVersion(4);

        self::assertEquals(1, $r->getFileVersionID());
        self::assertEquals(2, $fv2->getFileVersionID());
        self::assertEquals(3, $fv3->getFileVersionID());
        self::assertEquals(4, $fv4b->getFileVersionID());
        self::assertEquals(4, $fv4->getFileVersionID());
        self::assertEquals($fv4, $fv4b);

        $fv3->approve();
        self::assertEquals(true, $fv3->isApproved());

        $f = \File::getByID($r->getFileID());
        $fv1 = $f->getVersion(1);
        self::assertEquals(false, $fv1->isApproved());
        $fva = $f->getApprovedVersion();
        self::assertEquals($fva, $fv3);
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
