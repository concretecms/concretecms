<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class AddAliasDefaultsBlockCommand extends DefaultsBlockCommand implements BatchableCommandInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'update_defaults';
    }
}
