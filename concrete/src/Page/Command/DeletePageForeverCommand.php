<?php

namespace Concrete\Core\Page\Command;

use League\Tactician\Bernard\QueueableCommand;

class DeletePageForeverCommand extends PageCommand implements QueueableCommand
{

    public function getName()
    {
        return 'delete_page_forever';
    }

}