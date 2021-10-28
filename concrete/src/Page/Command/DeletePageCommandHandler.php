<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Workflow\Request\DeletePageRequest;

class DeletePageCommandHandler
{

    public function __invoke(DeletePageCommand $command)
    {
        $c = Page::getByID($command->getPageID());
        if ($c && !$c->isError()) {
            if ($c->getCollectionID() > 1) {
                $pkr = new DeletePageRequest();
                $pkr->setRequestedPage($c);
                $pkr->setRequesterUserID($command->getUserID());
                return $pkr->trigger();
            }
        }
    }


}