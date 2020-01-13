<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Permission\Access\Entity\FileUploaderEntity;

class FileUploader extends AccessEntity
{

    public function deliverEntity()
    {
        return FileUploaderEntity::getOrCreate();
    }
}
