<?php
namespace Concrete\Core\Permission\Registry\Multisite\Access;

use Concrete\Core\Permission\Registry\AbstractAccessRegistry;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\FileUploaderEntity;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Registry\Entry\Access\PermissionsEntry;

class DefaultRootFileFolderAccessRegistry extends AbstractAccessRegistry
{

    public function __construct()
    {
        $this->addEntry(new PermissionsEntry(new GroupEntity('/Sites'), [
            'search_file_folder',
        ]));
        $this->addEntry(new PermissionsEntry(new FileUploaderEntity(), [
            'edit_file_folder_file_properties',
            'edit_file_folder_file_contents',
            'copy_file_folder_files',
            'delete_file_folder_files'
        ]));
    }


}
