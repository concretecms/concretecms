<?php

namespace Concrete\Core\Board\Instance\Slot\Planner;

use Concrete\Core\Board\Instance\Slot\Content\ItemObjectGroup;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\SlotTemplate;

/**
 * Simple object to tie a board instance to a set of a InstanceItem objects. These get decreased over time
 * as the board slots are filled out.
 */
class PlannedInstance
{

    /**
     * @var Instance
     */
    protected $instance;

    /**
     * @var ItemObjectGroup[]
     */
    protected $contentObjectGroups = [];

    /**
     * @var PlannedSlot[]
     */
    protected $plannedSlots = [];

    /**
     * PlannedInstance constructor.
     * @param Instance $instance
     * @param ItemObjectGroup[] $contentObjectGroups
     */
    public function __construct(Instance $instance, array $contentObjectGroups)
    {
        $this->instance = $instance;
        $this->contentObjectGroups = $contentObjectGroups;
    }


    /**
     * @return Instance
     */
    public function getInstance(): Instance
    {
        return $this->instance;
    }

    /**
     * @return ItemObjectGroup[]
     */
    public function getContentObjectGroups(): array
    {
        return $this->contentObjectGroups;
    }

    /**
     * Retrieves object groups without removing them from the source array.
     *
     * @param int $number
     * @return ItemObjectGroup[]
     */
    public function sliceContentObjectGroups(int $number)
    {
        return array_slice($this->contentObjectGroups, 0, $number);
    }

    /**
     * Once object groups are used, remove them from the source array so they're not used again.
     *
     * @param int $number
     */
    public function removeObjectGroups(int $number): void
    {
        $this->contentObjectGroups = array_slice($this->contentObjectGroups, $number);
    }

    public function getPlannedSlot(int $slot)
    {
        $key = $slot - 1;
        return $this->plannedSlots[$key];
    }

    /**
     * @return PlannedSlot[]
     */
    public function getPlannedSlots(): array
    {
        return $this->plannedSlots;
    }

    public function addPlannedSlot(PlannedSlot $plannedSlot)
    {
        $this->plannedSlots[] = $plannedSlot;
    }


}