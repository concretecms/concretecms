<?php
namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Entity\Board\SlotTemplate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class CollectionFactory
{
    
    /**
     * @var EntityManager 
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $slotTemplatesByFormFactor;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $slotTemplates = $entityManager->getRepository(SlotTemplate::class)->findAll();
        foreach($slotTemplates as $slotTemplate) {
            $this->slotTemplatesByFormFactor[$slotTemplate->getFormFactor()][] = $slotTemplate;
        }
    }

    protected function getTemplateForSlot(Instance $instance, int $slot)
    {
        $driver = $instance->getBoard()->getTemplate()->getDriver();
        $formFactor = $driver->getFormFactor();
        if (is_array($formFactor)) {
            $formFactor = $formFactor[$slot];
        } else {
            $formFactor = $driver->getFormFactor();
        }
        $filteredSlots = $this->slotTemplatesByFormFactor[$formFactor];
        shuffle($filteredSlots);
        return $filteredSlots[0];
    }
    
    public function createSlotCollection(Instance $instance) : ArrayCollection
    {
        $collection = new ArrayCollection();
        $driver = $instance->getBoard()->getTemplate()->getDriver();
        $slots = $driver->getTotalSlots();
        for ($i = 1; $i <= $slots; $i++) {
            $template = $this->getTemplateForSlot($instance, $i);
            $slot = new InstanceSlot();
            $slot->setSlot($i);
            $slot->setInstance($instance);
            $slot->setTemplate($template);
            
            $collection->add($slot);
        }
        return $collection;
    }

}
