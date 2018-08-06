<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class RescanMultilingualPageCommand extends PageCommand implements BatchableCommandInterface
{

    public static function getBatchHandle()
    {
        return 'rescan_multilingual_page';
    }

}