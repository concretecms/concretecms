<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class AddAliasDefaultsBlockCommand extends DefaultsBlockCommand implements QueueableCommand
{

    public function getName()
    {
        return 'update_defaults';
    }

}