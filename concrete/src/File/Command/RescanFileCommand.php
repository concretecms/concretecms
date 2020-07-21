<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class RescanFileCommand extends FileCommand implements BatchableCommandInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'rescan_file';
    }
}
