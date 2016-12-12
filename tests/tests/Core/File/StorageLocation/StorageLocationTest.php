<?php
namespace Concrete\Tests\Core\File\StorageLocation;

use Concrete\Core\File\StorageLocation\Type\Type;
use Concrete\Core\File\StorageLocation\StorageLocation;

class StorageLocationTest extends \FileStorageTestCase
{
    public function testDefaultStorageLocation()
    {
        $type = Type::add('default', t('Default'));
        $configuration = $type->getConfigurationObject();
        $fsl = StorageLocation::add($configuration, 'Default', true);

        $fsl = StorageLocation::getByID(1);
        $configuration = $fsl->getConfigurationObject();
        $this->assertEquals(DIR_FILES_UPLOADED_STANDARD, $configuration->getRootPath());
        $this->assertEquals('http://www.dummyco.com/path/to/server/application/files/test.txt', $configuration->getPublicURLToFile('/test.txt'));
    }

    public function testCreateStorageLocations()
    {
        $location = $this->getStorageLocation();
        $this->assertEquals(true, $location->isDefault());
        $this->assertInstanceOf('\Concrete\Core\Entity\File\StorageLocation\StorageLocation', $location);

        $loc2 = StorageLocation::getByID(1);
        $this->assertEquals($loc2, $location);
        $this->assertInstanceOf('\Concrete\Core\File\StorageLocation\Configuration\LocalConfiguration',
            $loc2->getConfigurationObject());

        $configuration = $loc2->getConfigurationObject();
        $this->assertEquals($this->getStorageDirectory(), $configuration->getRootPath());
    }

    public function testStorageLocationPublicURLs()
    {
        $location = $this->getStorageLocation();
        $configuration = $location->getConfigurationObject();
        $this->assertEquals(true, $configuration->hasPublicURL());
        $this->assertEquals(true, $configuration->hasRelativePath());

        $configuration = $location->getConfigurationObject();
        $configuration->setWebRootRelativePath(null);
        $location->setConfigurationObject($configuration);
        $location->save();

        $configuration = $location->getConfigurationObject();
        $this->assertEquals(false, $configuration->hasPublicURL());
        $this->assertEquals(false, $configuration->hasRelativePath());
    }

    public function testGetFilesystemObject()
    {
        $location = $this->getStorageLocation();
        $filesystem = $location->getFileSystemObject();
        $this->assertInstanceOf('\League\Flysystem\Filesystem', $filesystem);
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
        $this->assertEquals('This is a text file.', $contents->read());
    }

    public function testBasicStreamFile()
    {
        $file = dirname(__FILE__) . '/fixtures/sample.txt';
        $starterSize = filesize($file);
        mkdir($this->getStorageDirectory());

        $src = fopen($file, 'rb+');

        $location = $this->getStorageLocation();
        $filesystem = $location->getFileSystemObject();
        $filesystem->writeStream('sample2.txt', $src);

        // now we should have the file in there.

        $this->assertTrue($filesystem->has('sample2.txt'));
        $fo = $filesystem->get('sample2.txt');
        $this->assertEquals($starterSize, $fo->getSize());
    }

    public function testGetByDefault()
    {
        $this->getStorageLocation();

        $type = Type::getByHandle('local');
        $configuration = $type->getConfigurationObject();
        StorageLocation::add($configuration, 'Other Storage');

        $location = StorageLocation::getDefault();
        $this->assertInstanceOf('\Concrete\Core\Entity\File\StorageLocation\StorageLocation', $location);
        $this->assertEquals(true, $location->isDefault());
        $this->assertEquals('Default', $location->getName());

        $alternate = StorageLocation::getByID(2);
        $this->assertInstanceOf('\Concrete\Core\Entity\File\StorageLocation\StorageLocation', $alternate);
        $this->assertEquals(false, $alternate->isDefault());
        $this->assertEquals('Other Storage', $alternate->getName());
    }
}
