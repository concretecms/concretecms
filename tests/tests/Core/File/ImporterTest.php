<?php
namespace Concrete\Tests\Core\File;
use Concrete\Core\Cache\CacheLocal;
use \Concrete\Core\File\StorageLocation\Type\Type;
use \Concrete\Core\File\StorageLocation\StorageLocation;
use \Concrete\Core\File\Importer;
use \Concrete\Core\Attribute\Type as AttributeType;
use \Concrete\Core\Attribute\Key\FileKey;
use Core;
use \Concrete\Core\Attribute\Key\Category;

class ImporterTest extends \FileStorageTestCase {

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'Files',
            'FileVersions',
            'Users',
            'PermissionAccessEntityTypes',
            'FileAttributeValues',
            'AttributeKeyCategories',
            'AttributeTypes',
            'Config',
            'AttributeKeys',
            'AttributeValues',
            'atNumber',
            'FileVersionLog'
        ));
        parent::setUp();
        define('UPLOAD_FILE_EXTENSIONS_ALLOWED', '*.txt;*.jpeg;*.png');

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

        $this->assertEquals(REL_DIR_FILES_UPLOADED_STANDARD . '/' . $apr[0] . '/' . $apr[1] . '/' . $apr[2] . '/test.txt',
            $r->getRelativePath()
        );

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

        $file = DIR_BASE . '/concrete/themes/default/images/inneroptics_dot_net_aspens.jpg';
        $fi = new Importer();
        $fo = $fi->import($file, 'Aspens.png');
        $type = $fo->getTypeObject();
        $this->assertEquals(\Concrete\Core\File\Type\Type::T_IMAGE, $type->getGenericType());

        $this->assertTrue((bool) $fo->hasThumbnail(1));
        $this->assertTrue((bool) $fo->hasThumbnail(2));
        $this->assertFalse((bool) $fo->hasThumbnail(3));

        $cf = Core::make('helper/concrete/file');
        $fh = Core::make('helper/file');
        $this->assertEquals('http://www.dummyco.com/application/files/thumbnails/level2'
            . $cf->prefix($fo->getPrefix(), $fh->replaceExtension($fo->getFilename(), 'jpg'), 2),
            $fo->getThumbnailURL(2));
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
        $this->assertFalse($fv2);

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
        $this->assertEquals(BASE_URL . '/application/files/' . $apr[0] . '/' . $apr[1] . '/' . $apr[2] . '/sample.txt',
            $r->getURL()
        );
    }

}
 