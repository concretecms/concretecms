<?php

namespace Concrete\Core\Board\Instance\Slot\Planner;

use Concrete\Core\Board\Instance\Slot\Content\ItemObjectGroup;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Entity\Board\SlotTemplate;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Joins a content slot to all content objects that could be present in it. This is then filtered by board rules
 * into the actual ContentObjectCollection objects passed to the PlannedSlotTemplate object.
 */
class PossibleContentObjectCollection
{
    
    /**
     * @var ArrayCollection
     */
    protected $availableContentObjects;

    public function __construct()
    {
        $this->availableContentObjects = new ArrayCollection();
    }

    /**
     * @param int $slot
     * @param ObjectInterface[] $objects
     */
    public function addContentObjects(int $slot, array $objects)
    {
        $this->availableContentObjects->set($slot, $objects);
    }

    public function getArray(): array
    {
        return $this->availableContentObjects->toArray();
    }



}

