<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class RescanFileCommand extends FileCommand implements QueueableCommand
{

    public function getName()
    {
        return 'rescan_file';
    }

}