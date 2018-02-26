<?php

namespace Concrete\Core\Page\Command;

use League\Tactician\Bernard\QueueableCommand;

class RescanMultilingualPageCommand extends PageCommand implements QueueableCommand
{

    public function getName()
    {
        return 'rescan_multilingual_page';
    }

}