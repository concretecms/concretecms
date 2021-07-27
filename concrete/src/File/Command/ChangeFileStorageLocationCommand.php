<?php

namespace Concrete\Core\File\Command;

class ChangeFileStorageLocationCommand extends FileCommand
{

    /**
     * @var integer
     */
    protected $storageLocationID;

    public function __construct(int $storageLocationID, int $fileID)
    {
        $this->storageLocationID = $storageLocationID;
        parent::__construct($fileID);
    }

    /**
     * @return int
     */
    public function getStorageLocationID(): int
    {
        return $this->storageLocationID;
    }




}
