<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class RescanMultilingualPageCommand extends PageCommand implements BatchableCommandInterface
{

    public function getBatchHandle()
    {
        return 'rescan_multilingual_page';
    }

}