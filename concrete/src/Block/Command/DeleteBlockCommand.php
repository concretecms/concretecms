<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class DeleteBlockCommand extends BlockCommand implements BatchableCommandInterface
{

    public static function getBatchHandle()
    {
        return 'delete_block';
    }

}