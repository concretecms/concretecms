<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class RescanFileCommand extends FileCommand implements BatchableCommandInterface
{

    public static function getBatchHandle()
    {
        return 'rescan_file';
    }


}