<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

abstract class FileCommand implements CommandInterface
{
    /**
     * @var int
     */
    protected $fileID;

    public function __construct(int $fileID)
    {
        $this->fileID = $fileID;
    }

    public function getFileID(): int
    {
        return $this->fileID;
    }
}
