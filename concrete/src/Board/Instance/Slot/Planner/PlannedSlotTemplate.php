<?php

namespace Concrete\Core\Board\Instance\Slot\Planner;

use Concrete\Core\Board\Instance\Slot\Content\ItemObjectGroup;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Entity\Board\SlotTemplate;

/**
 * Actually responsible for grouping selected content object collections with the proper template. This is then joined
 * back to the slot object.
 */
class PlannedSlotTemplate
{

    /**
     * @var SlotTemplate
     */
    protected $slotTemplate;

    /**
     * @var ObjectCollection
     */
    protected $objectCollection;

    /**
     * @return SlotTemplate
     */
    public function getSlotTemplate(): SlotTemplate
    {
        return $this->slotTemplate;
    }

    /**
     * @param SlotTemplate $slotTemplate
     */
    public function setSlotTemplate(SlotTemplate $slotTemplate): void
    {
        $this->slotTemplate = $slotTemplate;
    }

    /**
     * @return ObjectCollection
     */
    public function getObjectCollection(): ObjectCollection
    {
        return $this->objectCollection;
    }

    /**
     * @param ObjectCollection $objectCollection
     */
    public function setObjectCollection(ObjectCollection $objectCollection): void
    {
        $this->objectCollection = $objectCollection;
    }




}

