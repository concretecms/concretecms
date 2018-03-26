<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Bus\Command\CommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class UpdateForkedAliasDefaultsBlockCommand extends DefaultsBlockCommand implements QueueableCommand
{

    public function getName()
    {
        return 'update_defaults';
    }

}