<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class RescanFileCommand extends FileCommand implements BatchableCommandInterface
{

    public function getBatchHandle()
    {
        return 'rescan_file';
    }


}