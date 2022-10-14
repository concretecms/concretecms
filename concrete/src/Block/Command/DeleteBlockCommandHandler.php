<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\Events\BlockDelete;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Page\Page;

class DeleteBlockCommandHandler
{

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }


    public function __invoke(DeleteBlockCommand $command)
    {
        $page = Page::getByID($command->getPageID(), $command->getCollectionVersionID());
        if ($page && !$page->isError()) {
            $b = Block::getByID($command->getBlockID(), $page, $command->getAreaHandle());
            if (is_object($b) && !$b->isError()) {
                $b->deleteBlock();

                $event = new BlockDelete($b, $page);
                $this->eventDispatcher->dispatch('on_block_delete', $event);

                $b->getBlockCollectionObject()->rescanDisplayOrder($command->getAreaHandle());
            }
        }

    }


}
