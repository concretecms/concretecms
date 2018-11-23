<?php

namespace Concrete\Tests\File;

use Concrete\Core\File\Filesystem;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class FilesystemTest extends ConcreteDatabaseTestCase
{
    /**
     * @var \Concrete\Core\File\Filesystem
     */
    protected $filesystem;

    protected $tables = [
        'Trees',
        'TreeFileFolderNodes',
        'TreeTypes',
        'TreeNodeTypes',
        'TreeNodes',
        'PermissionKeys',
        'PermissionKeyCategories',
        'TreeNodePermissionAssignments',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->filesystem = new Filesystem();
        $this->filesystem->create();
    }

    public function testRootFolder()
    {
        $folder = $this->filesystem->getRootFolder();
        $this->assertNotNull($folder);
        $this->assertInstanceOf('Concrete\Core\Tree\Node\Type\FileFolder', $folder);
        $this->assertEquals(0, $folder->getTreeNodeParentID());
    }

    public function testAdd()
    {
        $folder = $this->filesystem->addFolder($this->filesystem->getRootFolder(), 'Test Sub Folder');
        $this->assertInstanceOf('Concrete\Core\Tree\Node\Type\FileFolder', $folder);
        $this->assertEquals(1, $folder->getTreeNodeParentID());
        $this->assertEquals('Test Sub Folder', $folder->getTreeNodeDisplayName());
    }

    public function testGetFolderByName()
    {
        $rootFolder = $this->filesystem->getRootFolder();
        $uniqueID = microtime(true) . '-' . mt_rand();
        // Create these folders:
        // |- Folder 1
        // |   |- Folder 1.1
        // |- Folder 2
        // |   |- Folder 1.2
        $folder_1 = $this->filesystem->addFolder($rootFolder, "Folder 1 {$uniqueID}");
        $folder_1_1 = $this->filesystem->addFolder($folder_1, "Folder 1.1 {$uniqueID}");
        $folder_2 = $this->filesystem->addFolder($rootFolder, "Folder 2 {$uniqueID}");
        $folder_2_1 = $this->filesystem->addFolder($folder_2, "Folder 2.1 {$uniqueID}");
        // Folder A should not be found
        $folderA = $rootFolder->getNodeByName("Folder A {$uniqueID}");
        $this->assertNull($folderA);
        // Add two Folder A:
        // |- Folder 1
        // |   |- Folder 1.1
        // |       |- Folder A
        // |- Folder 2
        // |   |- Folder 1.2
        // |       |- Folder A
        $folderA_1_1 = $this->filesystem->addFolder($folder_1_1, "Folder A {$uniqueID}");
        $folderA_2_1 = $this->filesystem->addFolder($folder_2_1, "Folder A {$uniqueID}");
        // Now Folder A can be one of the two Folder A created (we don't know which one)
        $folderA = $rootFolder->getNodeByName("Folder A {$uniqueID}");
        $this->assertNotNull($folderA);
        $this->assertContains($folderA->getTreeNodeID(), [$folderA_1_1->getTreeNodeID(), $folderA_2_1->getTreeNodeID()]);
        // Let's look for Folder A child of 1 -> 1.1
        $folderA = $rootFolder->getChildFolderByPath(["Folder 1 {$uniqueID}", "Folder 1.1 {$uniqueID}", "Folder A {$uniqueID}"]);
        $this->assertNotNull($folderA);
        $this->assertSame($folderA->getTreeNodeID(), $folderA_1_1->getTreeNodeID());
        // Let's look for Folder A child of 2 -> 2.1
        $folderA = $rootFolder->getChildFolderByPath(["Folder 2 {$uniqueID}", "Folder 2.1 {$uniqueID}", "Folder A {$uniqueID}"]);
        $this->assertNotNull($folderA);
        $this->assertSame($folderA->getTreeNodeID(), $folderA_2_1->getTreeNodeID());
        // Let getChildFolderByPath create folders that don't exist yet
        foreach ([false, true] as $create) {
            $folderB = $rootFolder->getChildFolderByPath(["Folder 3 {$uniqueID}", "Folder 3.1 {$uniqueID}", "Folder B {$uniqueID}"], $create);
            if ($create === false) {
                $this->assertNull($folderB);
            } else {
                $this->assertNotNull($folderB);
            }
        }
    }
}
