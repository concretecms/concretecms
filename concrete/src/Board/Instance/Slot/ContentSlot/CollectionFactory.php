<?php
namespace Concrete\Core\Board\Instance\Slot\ContentSlot;

use Concrete\Core\Entity\Board\InstanceContentSlot;
use Concrete\Core\Entity\Board\InstanceSlot;
use Doctrine\Common\Collections\ArrayCollection;

class CollectionFactory
{
    
    public function createContentSlotCollection(InstanceSlot $instanceSlot) : ArrayCollection
    {
        $collection = new ArrayCollection();
        $driver = $instanceSlot->getTemplate()->getDriver();
        $slots = $driver->getTotalContentSlots();
        for ($i = 1; $i <= $slots; $i++) {
            $slot = new InstanceContentSlot();
            $slot->setSlot($i);
            $slot->setInstanceSlot($instanceSlot);
            $collection->add($slot);
        }
        return $collection;
    }

}
