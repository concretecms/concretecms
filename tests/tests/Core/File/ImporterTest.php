<?php
namespace Concrete\Tests\Core\File;

use Concrete\Core\Cache\CacheLocal;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\File\Importer;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Core\Attribute\Key\FileKey;
use Config;
use Core;
use Concrete\Core\Attribute\Key\Category;

class ImporterTest extends \FileStorageTestCase
{
    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'Users',
            'PermissionAccessEntityTypes',
            'FileImageThumbnailTypes',
            'FilePermissionAssignments',
            'ConfigStore',
            'Logs',
            'FileVersionLog',
        ));
        $this->metadatas = array_merge($this->metadatas, array(
            'Concrete\Core\Entity\Attribute\Key\Type\NumberType',
            'Concrete\Core\Entity\Attribute\Key\Type\Type',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Value\Value',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Value\Value\Value',
            'Concrete\Core\Entity\Attribute\Type',
            'Concrete\Core\Entity\Attribute\Category',
        ));
        parent::setUp();
        Config::set('concrete.upload.extensions', '*.txt;*.jpg;*.jpeg;*.png');

        $category = Category::add('file');
        $number = AttributeType::add('number', 'Number');
        FileKey::add($number, array('akHandle' => 'width', 'akName' => 'Width'));
        FileKey::add($number, array('akHandle' => 'height', 'akName' => 'Height'));

        CacheLocal::flush();
    }

    protected function cleanup()
    {
        parent::cleanup();
        if (file_exists(dirname(__FILE__) . '/test.txt')) {
            unlink(dirname(__FILE__) . '/test.txt');
        }
        if (file_exists(dirname(__FILE__) . '/test.invalid')) {
            unlink(dirname(__FILE__) . '/test.invalid');
        }
    }

    public function testFileNotFound()
    {
        $fi = new Importer();
        $r = $fi->import('foo.txt', 'foo.txt');
        $this->assertEquals($r, Importer::E_FILE_INVALID);
    }

    public function testFileInvalidExtension()
    {
        $file = dirname(__FILE__) . '/test.invalid';
        touch($file);
        $fi = new Importer();
        $r = $fi->import($file, 'test.invalid');
        $this->assertEquals($r, Importer::E_FILE_INVALID_EXTENSION);
    }

    public function testFileInvalidStorageLocation()
    {
        $file = dirname(__FILE__) . '/test.txt';
        touch($file);
        $fi = new Importer();
        $r = $fi->import($file, 'test.txt');
        $this->assertEquals($r, Importer::E_FILE_INVALID_STORAGE_LOCATION);
    }

    public function testFileValid()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = dirname(__FILE__) . '/test.txt';
        touch($file);
        $fi = new Importer();
        $r = $fi->import($file, 'test.txt');

        $this->assertInstanceOf('\Concrete\Core\File\Version', $r);
        $this->assertEquals($r->getFileVersionID(), 1);
        $this->assertEquals($r->getFileID(), 1);
        $this->assertEquals('test.txt', $r->getFilename());
        $fo = $r->getFile();
        $fsl = $fo->getFileStorageLocationObject();
        $this->assertEquals(true, $fsl->isDefault());
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\StorageLocation', $fsl);
        $apr = str_split($r->getPrefix(), 4);

        $this->assertEquals('/application/files/' . $apr[0] . '/' . $apr[1] . '/' . $apr[2] . '/test.txt',
            $r->getRelativePath()
        );
    }

    public function testFileVersions()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = dirname(__FILE__) . '/test.txt';
        touch($file);
        $fi = new Importer();
        $fi->import($file, 'test.txt');

        $f = \File::getByID(1);
        $versions = $f->getFileVersions();
        $this->assertEquals(1, count($versions));
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
        $this->assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

        $this->assertEquals(113, $fo->getAttribute('width'));
        $this->assertEquals(113, $fo->getAttribute('height'));
    }
    public function testThumbnailStorageLocation()
    {
        mkdir($this->getStorageDirectory());
        $fsl = $this->getStorageLocation();

        $helper = Core::make('helper/concrete/file');
        $path = $helper->getThumbnailFilePath('137803870092', 'testing.gif', 1);
        $this->assertEquals('/thumbnails/1378/0387/0092/testing.jpg', $path);
    }

    public function testImageImport()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = DIR_BASE . '/concrete/themes/elemental/images/background-slider-night-road.png';
        $fi = new Importer();
        $fo = $fi->import($file, 'background-slider-night-road.png');
        $type = $fo->getTypeObject();
        $this->assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

        $this->assertTrue((bool) $fo->hasThumbnail(1));
        $this->assertTrue((bool) $fo->hasThumbnail(2));
        $this->assertFalse((bool) $fo->hasThumbnail(3));

        $cf = Core::make('helper/concrete/file');
        $fh = Core::make('helper/file');
        $this->assertEquals('http://www.dummyco.com/application/files/thumbnails/file_manager_detail'
            . $cf->prefix($fo->getPrefix(), $fh->replaceExtension($fo->getFilename(), 'jpg'), 2),
            $fo->getThumbnailURL('file_manager_detail'));
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
        $this->assertInstanceOf('\Concrete\Core\File\Version', $fo);
        $type = $fo->getTypeObject();
        $this->assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

        $this->assertTrue((bool) $fo->hasThumbnail(1));
        $this->assertTrue((bool) $fo->hasThumbnail(2));
        $this->assertFalse((bool) $fo->hasThumbnail(3));

        $cf = Core::make('helper/concrete/file');
        $fh = Core::make('helper/file');
        $this->assertEquals('http://www.dummyco.com/application/files/thumbnails/file_manager_detail'
            . $cf->prefix($fo->getPrefix(), $fh->replaceExtension($fo->getFilename(), 'jpg'), 2),
            $fo->getThumbnailURL('file_manager_detail'));
    }

    public function testFileVersionDelete()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = dirname(__FILE__) . '/StorageLocation/fixtures/sample.txt';
        $fi = new Importer();
        $fi->import($sample, 'sample.txt');

        $f = \File::getByID(1);
        $fv = $f->getVersion(1);
        $this->assertEquals('sample.txt', $fv->getFilename());
        $fv->delete();

        CacheLocal::flush();

        $fv2 = $f->getVersion(1);
        $this->assertNull($fv2);
    }

    public function testImporterMimeType()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = dirname(__FILE__) . '/StorageLocation/fixtures/sample.txt';
        $fi = new Importer();
        $fo1 = $fi->import($sample, 'sample.txt');

        $sample = dirname(__FILE__) . '/StorageLocation/fixtures/gummies.txt';
        $fi = new Importer();
        $fo2 = $fi->import($sample, 'gummies.txt');

        $this->assertEquals('text/plain', $fo1->getMimeType());
        $this->assertEquals('image/jpeg', $fo2->getMimeType());
    }

    public function testFileDuplicate()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = dirname(__FILE__) . '/StorageLocation/fixtures/sample.txt';
        $fi = new Importer();
        $fi->import($sample, 'sample.txt');

        $f = \File::getByID(1);
        $f2 = $f->duplicate();
        $this->assertEquals(2, $f2->getFileID());
        $versions = $f2->getVersionList();
        $this->assertEquals(1, count($versions));
        $this->assertEquals(1, $versions[0]->getFileVersionID());
        $this->assertEquals(2, $versions[0]->getFileID());
    }

    public function testFileVersionDuplicate()
    {
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $sample = dirname(__FILE__) . '/StorageLocation/fixtures/sample.txt';
        $fi = new Importer();
        $fi->import($sample, 'sample.txt');

        $f = \File::getByID(1);
        $fv = $f->getVersion(1);

        $this->assertEquals(1, $fv->getFileVersionID());
        $this->assertEquals('sample.txt', $fv->getFilename());
        $this->assertEquals(true, $fv->isApproved());

        $fv2 = $fv->duplicate();
        $this->assertEquals(2, $fv2->getFileVersionID());
        $this->assertEquals(false, $fv->isApproved());
    }

    public function testFileReplace()
    {

        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = dirname(__FILE__) . '/test.txt';
        touch($file);
        $fi = new Importer();
        $fo = $fi->import($file, 'test.txt');
        $fo = $fo->getFile();

        $sample = dirname(__FILE__) . '/StorageLocation/fixtures/sample.txt';
        $r = $fi->import($sample, 'sample.txt', $fo);

        $this->assertInstanceOf('\Concrete\Core\File\Version', $r);
        $this->assertEquals(2, $r->getFileVersionID());
        $this->assertEquals('sample.txt', $r->getFilename());
        $apr = str_split($r->getPrefix(), 4);
        $this->assertEquals('http://www.dummyco.com/application/files/' . $apr[0] . '/' . $apr[1] . '/' . $apr[2] . '/sample.txt',
            $r->getURL()
        );
    }

    public function testVersionApprove()
    {
        // create the default storage location first.
        mkdir($this->getStorageDirectory());
        $this->getStorageLocation();

        $file = dirname(__FILE__) . '/test.txt';
        touch($file);
        $fi = new Importer();
        $r = $fi->import($file, 'test.txt');

        $fv2 = $r->duplicate();
        $fv3 = $r->duplicate();
        $fv4 = $r->duplicate();
        $f = \File::getByID(1);
        $fv4b = $f->getVersion(4);

        $this->assertEquals(1, $r->getFileVersionID());
        $this->assertEquals(2, $fv2->getFileVersionID());
        $this->assertEquals(3, $fv3->getFileVersionID());
        $this->assertEquals(4, $fv4b->getFileVersionID());
        $this->assertEquals(4, $fv4->getFileVersionID());
        $this->assertEquals($fv4, $fv4b);

        $fv3->approve();
        $this->assertEquals(true, $fv3->isApproved());

        $f = \File::getByID(1);
        $fv1 = $f->getVersion(1);
        $this->assertEquals(false, $fv1->isApproved());
        $fva = $f->getApprovedVersion();
        $this->assertEquals($fva, $fv3);
    }
}
