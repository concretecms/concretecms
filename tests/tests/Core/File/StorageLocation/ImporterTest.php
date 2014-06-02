<?php
namespace Concrete\Tests\Core\File\StorageLocation;
use \Concrete\Core\File\StorageLocation\Type\Type;
use \Concrete\Core\File\StorageLocation\StorageLocation;
use \Concrete\Core\File\Importer;

class ImporterTest extends \FileStorageTestCase {

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'Files',
            'FileVersions',
            'Users',
            'PermissionAccessEntityTypes'
        ));
        parent::setUp();
        define('UPLOAD_FILE_EXTENSIONS_ALLOWED', '*.txt;*.jpeg');
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

    }

}
 