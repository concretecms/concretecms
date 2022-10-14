<?php

namespace Concrete\Core\Block\Command;

use Concrete\Core\Block\Events\BlockEdit;
use Concrete\Core\Events\EventDispatcher;

class UpdatePageBlockCommandHandler
{

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(UpdatePageBlockCommand $command)
    {
        $page = $command->getPage();
        $block = $command->getBlock();
        $data = $command->getData();

        $block->update($data);
        $event = new BlockEdit($block, $page);
        $this->dispatcher->dispatch('on_block_edit', $event);

        return $block;
    }


}
