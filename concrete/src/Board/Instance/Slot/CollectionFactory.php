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
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param SlotTemplate[] $availableTemplates
     * @param Instance $instance
     * @param int $slot
     * @return mixed
     */
    protected function getTemplateForSlot($availableTemplates, Instance $instance, int $slot)
    {
        $availableTemplatesByFormFactor = [];
        foreach($availableTemplates as $availableTemplate) {
            $availableTemplatesByFormFactor[$availableTemplate->getFormFactor()][] = $availableTemplate;
        }
        
        $driver = $instance->getBoard()->getTemplate()->getDriver();
        $formFactor = $driver->getFormFactor();
        if (is_array($formFactor)) {
            $formFactor = $formFactor[$slot];
        } else {
            $formFactor = $driver->getFormFactor();
        }
        
        $filteredSlots = $availableTemplatesByFormFactor[$formFactor];
        shuffle($filteredSlots);
        return $filteredSlots[0];
    }
    
    public function createSlotCollection(Instance $instance) : ArrayCollection
    {
        $board = $instance->getBoard();
        if ($board->hasCustomSlotTemplates()) {
            $availableTemplates = $board->getCustomSlotTemplates();
        } else {
            $availableTemplates = $this->entityManager->getRepository(SlotTemplate::class)->findAll();
        }
        $collection = new ArrayCollection();
        $driver = $board->getTemplate()->getDriver();
        $slots = $driver->getTotalSlots();
        for ($i = 1; $i <= $slots; $i++) {
            $template = $this->getTemplateForSlot($availableTemplates, $instance, $i);
            $slot = new InstanceSlot();
            $slot->setSlot($i);
            $slot->setInstance($instance);
            $slot->setTemplate($template);
            
            $collection->add($slot);
        }
        return $collection;
    }

}
