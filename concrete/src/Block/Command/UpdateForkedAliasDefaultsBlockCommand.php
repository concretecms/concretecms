<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class UpdateForkedAliasDefaultsBlockCommand extends DefaultsBlockCommand implements BatchableCommandInterface
{

    public function getBatchHandle()
    {
        return 'update_defaults';
    }

}