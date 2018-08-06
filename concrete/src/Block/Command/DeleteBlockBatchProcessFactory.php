<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use League\Tactician\Bernard\QueueableCommand;

class DeleteBlockBatchProcessFactory implements BatchProcessFactoryInterface
{

    public function getBatchHandle()
    {
        return 'delete_block';
    }

    public function getCommands($blocks): array
    {
        $commands = [];
        foreach($blocks as $b) {
            $commands[] = new DeleteBlockCommand(
                $b['bID'], $b['cID'], $b['cvID'], $b['arHandle']
            );
        }
        return $commands;
    }

}