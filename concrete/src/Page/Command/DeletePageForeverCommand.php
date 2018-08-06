<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;
use League\Tactician\Bernard\QueueableCommand;

class DeletePageForeverCommand extends PageCommand implements BatchableCommandInterface
{

    public static function getBatchHandle()
    {
        return 'delete_page_forever';
    }

}