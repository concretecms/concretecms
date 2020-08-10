<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class DeletePageForeverCommand extends PageCommand implements BatchableCommandInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'delete_page_forever';
    }
}
