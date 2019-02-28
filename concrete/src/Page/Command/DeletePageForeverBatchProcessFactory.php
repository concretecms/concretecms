<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Entity\File\File;
use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;
use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class DeletePageForeverBatchProcessFactory implements BatchProcessFactoryInterface
{

    public function getBatchHandle()
    {
        return 'delete_page_forever';
    }

    public function getCommands($pages) : array
    {
        $commands = [];
        foreach ($pages as $cID) {
            $commands[] = new DeletePageForeverCommand($cID);
        }
        return $commands;
    }
}