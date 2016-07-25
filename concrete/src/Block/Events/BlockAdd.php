<?php
namespace Concrete\Core\Block\Events;

use Concrete\Core\Block\Block;
use Concrete\Core\Page\Page;

class BlockAdd extends BlockEvent
{

    public function __construct(Block $block, Page $page, array $arguments = array())
    {
        parent::__construct($block, $arguments);
        $this->setArgument('page', $page);
    }

}
