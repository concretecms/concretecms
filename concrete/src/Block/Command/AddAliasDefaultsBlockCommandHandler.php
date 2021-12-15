<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\Command\DeleteBlockCommand;
use Concrete\Core\Page\Page;

class AddAliasDefaultsBlockCommandHandler
{

    public function __invoke(AddAliasDefaultsBlockCommand $command)
    {
        $page = Page::getByID($command->getPageID(), $command->getCollectionVersionID());
        if ($page && !$page->isError()) {
            $mc = Page::getByID($command->getOriginalPageID(), $command->getOriginalCollectionVersionID());
            if ($mc && !$mc->isError()) {
                $b = Block::getByID($command->getOriginalBlockID(), $mc, $command->getOriginalAreaHandle());
                if ($b) {
                    $bt = $b->getBlockTypeObject();
                    $displayOrder = $b->getBlockDisplayOrder();
                    if ($bt->isCopiedWhenPropagated()) {
                        $b = $b->duplicate($page, true);
                        $b->setAbsoluteBlockDisplayOrder($displayOrder);
                    } else {
                        $b->alias($page, $displayOrder);
                    }

                    $page->rescanDisplayOrder($command->getAreaHandle());
                }
            }
        }
    }


}