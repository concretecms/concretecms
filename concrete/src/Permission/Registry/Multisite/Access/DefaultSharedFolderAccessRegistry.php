<?php
namespace Concrete\Core\Permission\Registry\Multisite\Access;

use Concrete\Core\Permission\Registry\AbstractAccessRegistry;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Registry\Entry\Access\PermissionsEntry;

class DefaultSharedFolderAccessRegistry extends AbstractAccessRegistry
{

    public function __construct()
    {
        $this->addEntry(new PermissionsEntry(new GroupEntity('/Sites'), [
            'search_file_folder',
            'add_file',
        ]));
    }


}
