<?php
namespace Concrete\Tests\Core\File\StorageLocation;
use \Concrete\Core\File\StorageLocation\Type\Type;
use \Concrete\Core\File\StorageLocation\StorageLocation;

class StorageLocationTest extends \ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array(
        'FileStorageLocationTypes',
        'FileStorageLocations',
    );

    protected function getStorageDirectory()
    {
        return dirname(__FILE__) . '/files';
    }

    protected function cleanup()
    {
        if (is_dir($this->getStorageDirectory())) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->getStorageDirectory(), \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileinfo->getRealPath());
            }

            rmdir($this->getStorageDirectory());
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $this->cleanup();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->cleanup();
    }


    public function testCreateStorageLocations()
    {
        $location = $this->getStorageLocation();
        $this->assertEquals(true, $location->isDefault());
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\StorageLocation', $location);

        $loc2 = StorageLocation::getByID(1);
        $this->assertEquals($loc2, $location);
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration',
            $loc2->getConfigurationObject());

        $configuration = $loc2->getConfigurationObject();
        $this->assertEquals($this->getStorageDirectory(), $configuration->getRootPath());

    }

    protected function getStorageLocation()
    {
        $type = Type::add('local', t('Local Storage'));
        $configuration = $type->getConfigurationObject();
        $configuration->setRootPath($this->getStorageDirectory());
        return StorageLocation::add($configuration, 'Default', true);

    }
    public function testGetFilesystemObject()
    {
        $location = $this->getStorageLocation();
        $filesystem = $location->getFileSystemObject();
        $this->assertInstanceOf('\Gaufrette\Filesystem', $filesystem);
    }

    /**
     * This handles storing a file in the filesystem without any
     * fancy concrete5 prefixing.
     */
    public function testBasicStoreFile()
    {
        mkdir($this->getStorageDirectory());
        $location = $this->getStorageLocation();
        $filesystem = $location->getFileSystemObject();
        $filesystem->write('foo.txt', 'This is a text file.');
        $this->assertTrue($filesystem->has('foo.txt'));

        $contents = $filesystem->get('foo.txt');
        $this->assertEquals('This is a text file.', $contents->getContent());
    }

    public function testGetByDefault()
    {
        $this->getStorageLocation();

        $type = Type::getByHandle('local');
        $configuration = $type->getConfigurationObject();
        StorageLocation::add($configuration, 'Other Storage');

        $location = StorageLocation::getDefault();
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\StorageLocation', $location);
        $this->assertEquals(true, $location->isDefault());
        $this->assertEquals('Default', $location->getName());

        $alternate = StorageLocation::getByID(2);
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\StorageLocation', $alternate);
        $this->assertEquals(false, $alternate->isDefault());
        $this->assertEquals('Other Storage', $alternate->getName());
    }


}
 