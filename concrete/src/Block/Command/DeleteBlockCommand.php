<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class DeleteBlockCommand extends BlockCommand implements BatchableCommandInterface
{

    public function getBatchHandle()
    {
        return 'delete_block';
    }

}