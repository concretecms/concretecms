<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;

class DeletePageForeverBatchProcessFactory implements BatchProcessFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'delete_page_forever';
    }

    /**
     * {@inheritdoc}
     *
     * @param int[] $pages
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getCommands()
     */
    public function getCommands($pages): array
    {
        $commands = [];
        foreach ($pages as $cID) {
            $commands[] = new DeletePageForeverCommand($cID);
        }

        return $commands;
    }
}
