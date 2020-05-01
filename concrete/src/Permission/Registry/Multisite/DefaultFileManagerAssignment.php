<?php
namespace Concrete\Core\Permission\Registry\Multisite;

use Concrete\Core\Permission\Registry\AssignmentInterface;
use Concrete\Core\Permission\Registry\Entry\Object\Object\FileFolder;
use Concrete\Core\Permission\Registry\Multisite\Access\DefaultRootFileFolderAccessRegistry;

class DefaultFileManagerAssignment implements AssignmentInterface
{

    public function getRegistry()
    {
        return new DefaultRootFileFolderAccessRegistry();
    }

    public function getEntry()
    {
        return new FileFolder('');
    }


}
