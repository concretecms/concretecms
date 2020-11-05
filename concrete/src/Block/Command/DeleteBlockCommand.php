<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class DeleteBlockCommand extends BlockCommand implements BatchableCommandInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'delete_block';
    }
}
