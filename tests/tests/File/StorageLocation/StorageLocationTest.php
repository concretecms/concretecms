<?php
namespace Concrete\Tests\File\StorageLocation;

use Concrete\Core\File\Filesystem;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Concrete\Core\File\StorageLocation\Type\Type;
use Concrete\TestHelpers\File\FileStorageTestCase;

class StorageLocationTest extends FileStorageTestCase
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

        $loc2 = StorageLocation::getByID($location->getID());
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
        $file = DIR_TESTS . '/assets/File/StorageLocation/sample.txt';
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
        $other = StorageLocation::add($configuration, 'Other Storage');

        $location = StorageLocation::getDefault();
        $this->assertInstanceOf('\Concrete\Core\Entity\File\StorageLocation\StorageLocation', $location);
        $this->assertEquals(true, $location->isDefault());
        $this->assertEquals('Default', $location->getName());

        $alternate = StorageLocation::getByID($other->getID());
        $this->assertInstanceOf('\Concrete\Core\Entity\File\StorageLocation\StorageLocation', $alternate);
        $this->assertEquals(false, $alternate->isDefault());
        $this->assertEquals('Other Storage', $alternate->getName());
    }

    public function testAddFolderWithLocation()
    {
        //Get a default storageLocation
        $fsl = $this->getStorageLocation();
        $storageLocationOne = StorageLocation::getByID($fsl->getID());

        // Add A non Default Storage Location
        $fsl = StorageLocation::add($fsl->getConfigurationObject(), 'NotDefault', false);
        $storageLocationTwo = StorageLocation::getByID($fsl->getID());

        // Get our filesystem
        $filesystem = new Filesystem();
        $folderOne = $filesystem->addFolder($filesystem->getRootFolder(), 'Test Default Storage Folder', $storageLocationOne);
        $this->assertInstanceOf('Concrete\Core\Tree\Node\Type\FileFolder', $folderOne);
        $this->assertNotEquals($storageLocationTwo->getID(), $folderOne->getTreeNodeStorageLocationID());
        $this->assertEquals($storageLocationOne->getID(), $folderOne->getTreeNodeStorageLocationID());
    }

    public function testMultipleFoldersWithLocations()
    {
        //Get a default storageLocation
        $fsl = $this->getStorageLocation();

        // This is just to make sure it loads from the DB
        $storageLocationOne = StorageLocation::getByID($fsl->getID());

        // Add A non Default Storage Location
        $fsl = StorageLocation::add($fsl->getConfigurationObject(), 'NotDefault', false);
        $storageLocationTwo = StorageLocation::getByID($fsl->getID());

        // Get our filesystem
        $filesystem = new Filesystem();

        // Add a folder with the default storage object
        $folderOne = $filesystem->addFolder($filesystem->getRootFolder(), 'Test Default Storage Folder', $storageLocationOne);
        $this->assertInstanceOf('Concrete\Core\Tree\Node\Type\FileFolder', $folderOne);
        $this->assertNotEquals($storageLocationTwo->getID(), $folderOne->getTreeNodeStorageLocationID());
        $this->assertEquals($storageLocationOne->getID(), $folderOne->getTreeNodeStorageLocationID());
        $this->assertTrue($folderOne->getTreeNodeStorageLocationObject()->isDefault());

        // Add a folder with the secondary non default storage object
        $folderTwo = $filesystem->addFolder($filesystem->getRootFolder(), 'Test Non Default Storage Folder', $storageLocationTwo);
        $this->assertInstanceOf('Concrete\Core\Tree\Node\Type\FileFolder', $folderTwo);
        $this->assertNotEquals($storageLocationOne->getID(), $folderTwo->getTreeNodeStorageLocationID());
        $this->assertEquals($storageLocationTwo->getID(), $folderTwo->getTreeNodeStorageLocationID());
        $this->assertNotTrue($folderTwo->getTreeNodeStorageLocationObject()->isDefault());

        // Add a folder without any storage Object
        $folderThree = $filesystem->addFolder($filesystem->getRootFolder(), 'Test Default Folder');
        $this->assertInstanceOf('Concrete\Core\Tree\Node\Type\FileFolder', $folderThree);
        $this->assertNull($folderThree->getTreeNodeStorageLocationObject());
    }
}
