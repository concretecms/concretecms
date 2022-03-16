<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\Command\DeleteBlockCommand;
use Concrete\Core\Page\Page;

class UpdateForkedAliasDefaultsBlockCommandHandler
{

    public function __invoke(UpdateForkedAliasDefaultsBlockCommand $command)
    {
        $page = Page::getByID($command->getPageID(), $command->getCollectionVersionID());
        if ($page && !$page->isError()) {
            $mc = Page::getByID($command->getOriginalPageID(), $command->getOriginalCollectionVersionID());
            if ($mc && !$mc->isError()) {
                $b = Block::getByID($command->getOriginalBlockID(), $mc, $command->getOriginalAreaHandle());
                if ($b) {
                    $forked = Block::getByID($command->getBlockID(), $page, $command->getAreaHandle());
                    if (is_object($forked) && !$forked->isError()) {
                        // take the current block that is in defaults, and replace the block on the page
                        // with that block.
                        if ($command->getForceDisplayOrder()) {
                            $existingDisplayOrder = $b->getBlockDisplayOrder();
                        } else {
                            $existingDisplayOrder = $forked->getBlockDisplayOrder();
                        }

                        $bt = $b->getBlockTypeObject();

                        // Now we delete the existing forked block.
                        $forked->deleteBlock();

                        if ($bt->isCopiedWhenPropagated()) {
                            $b = $b->duplicate($page, 'duplicate_master');
                        } else {
                            $b->alias($page);
                            $b = \Block::getByID($b->getBlockID(), $page, $command->getAreaHandle());
                        }

                        $b->setAbsoluteBlockDisplayOrder($existingDisplayOrder);
                        $page->rescanDisplayOrderFromBlock($b, $command->getAreaHandle(), 0);
                    }
                }
            }
        }
    }


}