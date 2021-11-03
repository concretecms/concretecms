<?php
namespace Concrete\Core\Permission\Registry\Multisite;

use Concrete\Core\Permission\Registry\AssignmentInterface;
use Concrete\Core\Permission\Registry\Entry\Object\Object\FileFolder;
use Concrete\Core\Permission\Registry\Multisite\Access\DefaultSharedFolderAccessRegistry;

class DefaultSharedFolderAssignment implements AssignmentInterface
{

    public function getRegistry()
    {
        return new DefaultSharedFolderAccessRegistry();
    }

    public function getEntry()
    {
        return new FileFolder('/' . t('Shared Files'));
    }


}
