<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class DeletePageForeverCommand extends PageCommand implements BatchableCommandInterface
{

    public function getBatchHandle()
    {
        return 'delete_page_forever';
    }

}