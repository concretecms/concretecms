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

    protected function createStorageLocation()
    {
        $type = Type::add('local', t('Local Storage'));
        $configuration = $type->getConfigurationObject();
        $configuration->setRootPath($this->getStorageDirectory());
        $location = StorageLocation::add($configuration, 'Default');
    }

    public function testCreateStorageLocations()
    {
        $location = $this->createStorageLocation();
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\StorageLocation', $location);

        $loc2 = StorageLocation::getByID(1);
        $this->assertEquals($loc2, $location);
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration',
            $loc2->getConfigurationObject());

        $configuration = $loc2->getConfigurationObject();
        $this->assertEquals($this->getStorageDirectory(), $configuration->getRootPath());
    }

    public function testGetStorageLocationAdapter()
    {
        $location = $this->createStorageLocation();
        $filesystem = $location->getFileSystemObject();
    }

}
 