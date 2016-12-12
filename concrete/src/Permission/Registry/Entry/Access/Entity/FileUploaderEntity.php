<?php
namespace Concrete\Core\Permission\Registry\Entry\Access\Entity;

use Concrete\Core\Permission\Access\Entity\FileUploaderEntity as FileUploaderPermissionsEntity;

class FileUploaderEntity implements EntityInterface
{

    public function getAccessEntity()
    {
        $uploader = FileUploaderPermissionsEntity::getOrCreate();
        return $uploader;
    }

}
