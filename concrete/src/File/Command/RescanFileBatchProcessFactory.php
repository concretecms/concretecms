<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface;

class RescanFileBatchProcessFactory implements BatchProcessFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'rescan_file';
    }

    /**
     * {@inheritdoc}
     *
     * @param \Concrete\Core\Entity\File\File[] $files
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\BatchProcessFactoryInterface::getCommands()
     */
    public function getCommands($files): array
    {
        $commands = [];
        foreach ($files as $file) {
            $commands[] = new RescanFileCommand($file->getFileID());
        }

        return $commands;
    }
}
