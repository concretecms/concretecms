<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\Command\DeleteBlockCommand;
use Concrete\Core\Page\Page;

class DeleteBlockCommandHandler
{

    public function __invoke(DeleteBlockCommand $command)
    {
        $page = Page::getByID($command->getPageID(), $command->getCollectionVersionID());
        if ($page && !$page->isError()) {
            $b = Block::getByID($command->getBlockID(), $page, $command->getAreaHandle());
            if (is_object($b) && !$b->isError()) {
                $b->deleteBlock();
            }
        }

    }


}