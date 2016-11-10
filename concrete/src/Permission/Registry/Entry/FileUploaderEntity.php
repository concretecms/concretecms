<?php
namespace Concrete\Core\Permission\Registry\Entry;

use Concrete\Core\Permission\Access\Entity\FileUploaderEntity as FileUploaderPermissionsEntity;

class FileUploaderEntity extends AbstractPermissionsEntry
{

    public function __construct($permissions)
    {
        $uploader = FileUploaderPermissionsEntity::getOrCreate();
        $this->setPermissonKeyHandles($permissions);
        $this->setAccessEntity($uploader);
    }

}
