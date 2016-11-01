<?php

namespace Concrete\Core\Block\Events;

use Concrete\Core\Block\Block;
use Concrete\Core\Page\Page;
use Symfony\Component\EventDispatcher\GenericEvent;

abstract class BlockEvent extends GenericEvent
{

    /**
     * Overridden to provide typehint
     * @return Block
     */
    public function getSubject()
    {
        return parent::getSubject();
    }

    /**
     * The page attached to this event
     * @return Page|null
     */
    public function getPage()
    {
        return $this->getArgument('page');
    }

}
