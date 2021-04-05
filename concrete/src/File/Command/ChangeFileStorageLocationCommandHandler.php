<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\File\File;
use Concrete\Core\File\StorageLocation\StorageLocationFactory;

class ChangeFileStorageLocationCommandHandler
{

    /**
     * @param ChangeFileStorageLocationCommand $command
     */
    public function __invoke(ChangeFileStorageLocationCommand $command)
    {
        $storageLocationFactory = app(StorageLocationFactory::class);
        $storageLocation = $storageLocationFactory->fetchByID($command->getStorageLocationID());
        $file = File::getByID($command->getFileID());
        if ($file && $storageLocation) {
            $file->setFileStorageLocation($storageLocation);
        }
    }


}