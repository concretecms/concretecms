<?php

namespace Concrete\Tests\File;

use Concrete\Core\File\Filesystem;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class FilesystemTest extends ConcreteDatabaseTestCase
{
    protected $filesystem;

    protected $tables = [
        'Trees',
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
}
