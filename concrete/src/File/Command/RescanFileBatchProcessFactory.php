<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;

class RescanFileBatchProcessFactory implements BatchProcessFactoryInterface
{

    public function getBatchHandle()
    {
        return 'rescan_file';
    }

    public function getCommands($files) : array
    {
        $commands = [];
        foreach($files as $file) {
            $commands[] = new RescanFileCommand($file->getFileID());
        }
        return $commands;
    }
}