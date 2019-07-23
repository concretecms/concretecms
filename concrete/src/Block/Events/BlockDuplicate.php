<?php

namespace Concrete\Core\Block\Events;

class BlockDuplicate extends BlockEvent
{
    /**
     * @return \Concrete\Core\Block\Block
     */
    public function getNewBlock()
    {
        return $this->getSubject();
    }

    /**
     * @return \Concrete\Core\Block\Block
     */
    public function getOldBlock()
    {
        return $this->getArgument('oldBlock');
    }

    public function setOldBlock($block)
    {
        $this->setArgument('oldBlock', $block);
    }
}
