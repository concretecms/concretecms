<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class DeleteBlockCommand extends BlockCommand implements QueueableCommand
{

    public function getName()
    {
        return 'delete_block';
    }

}