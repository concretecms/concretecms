<?php

namespace Concrete\Core\Page;

/**
 * @since 8.5.0
 */
class DisplayOrderUpdateEvent extends Event
{
    /**
     * Get the old display order
     *
     * This is the order *before* the page was moved.
     *
     * @return int
     */
    public function getOldDisplayOrder()
    {
        return (int) $this->getArgument('oldDisplayOrder');
    }

    /**
     * Get the new display order
     *
     * This is the order *after* the page was moved.
     *
     * @return int
     */
    public function getNewDisplayOrder()
    {
        return (int) $this->getArgument('newDisplayOrder');
    }

    /**
     * @param int $displayOrder
     */
    public function setOldDisplayOrder($displayOrder)
    {
        $this->setArgument('oldDisplayOrder', (int) $displayOrder);
    }

    /**
     * @param int $displayOrder
     */
    public function setNewDisplayOrder($displayOrder)
    {
        $this->setArgument('newDisplayOrder', (int) $displayOrder);
    }
}
