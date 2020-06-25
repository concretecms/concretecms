<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;

class DeleteBlockBatchProcessFactory implements BatchProcessFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'delete_block';
    }

    /**
     * {@inheritdoc}
     *
     * @param array[] $blocks the blocks data. Every array item is an array with keys 'bID', 'cID', 'cvID', 'arHandle'
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getCommands()
     */
    public function getCommands($blocks): array
    {
        $commands = [];
        foreach ($blocks as $b) {
            $commands[] = new DeleteBlockCommand(
                $b['bID'],
                $b['cID'],
                $b['cvID'],
                $b['arHandle']
            );
        }

        return $commands;
    }
}
