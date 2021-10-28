<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Entity\Board\Instance;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Holds a collection of RenderedSlot objects, each bound to a particular slot for easy rendering.
 */
class RenderedSlotCollection
{

    /**
     * @var Instance
     */
    protected $instance;

    public function __construct(Instance $instance, array $collection = [])
    {
        $this->instance = $instance;
        $this->slots = new ArrayCollection($collection);
    }


    public function getRenderedSlot(int $slot): ?RenderedSlot
    {
        return $this->slots->get($slot);
    }


}

