<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

abstract class FileCommand implements CommandInterface
{

    protected $fileID;

    /**
     * FileCommand constructor.
     * @param $fID
     */
    public function __construct($fileID)
    {
        $this->fileID = $fileID;
    }

    /**
     * @return mixed
     */
    public function getFileID()
    {
        return $this->fileID;
    }



}