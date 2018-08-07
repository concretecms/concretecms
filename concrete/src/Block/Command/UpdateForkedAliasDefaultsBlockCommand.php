<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class UpdateForkedAliasDefaultsBlockCommand extends DefaultsBlockCommand implements BatchableCommandInterface
{

    public function getBatchHandle()
    {
        return 'update_defaults';
    }

}