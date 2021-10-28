<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Command\Command;

abstract class FileCommand extends Command
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
