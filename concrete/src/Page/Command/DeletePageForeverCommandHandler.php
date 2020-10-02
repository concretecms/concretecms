<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;

class DeletePageForeverCommandHandler
{

    public function __invoke(DeletePageForeverCommand $command)
    {
        $c = Page::getByID($command->getPageID());
        if ($c && !$c->isError()) {
            $c->delete();
        }
    }


}