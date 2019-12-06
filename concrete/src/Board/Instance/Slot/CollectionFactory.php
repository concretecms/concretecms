<?php
namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Board\Instance\Slot\Content\ItemObjectGroup;
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
     * @return SlotTemplate
     */
    protected function getTemplateForSlot($availableTemplates, Instance $instance, int $slot, int $totalItemsRemaining)
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
        
        $filteredTemplates = $availableTemplatesByFormFactor[$formFactor];
        shuffle($filteredTemplates);
        
        foreach($filteredTemplates as $filteredTemplate) {
            if ($filteredTemplate->getDriver()->getTotalContentSlots() <= $totalItemsRemaining) {
                return $filteredTemplate;
            }
        }
    }

    /**
     * @param Instance $instance
     * @param ItemObjectGroup[] $contentObjectGroups
     * @return ArrayCollection
     */
    public function createSlotCollection(Instance $instance, array $contentObjectGroups) : ArrayCollection
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

        $totalItemsRemaining = count($contentObjectGroups);
        
        for ($i = 1; $i <= $slots; $i++) {
            $template = $this->getTemplateForSlot($availableTemplates, $instance, $i, $totalItemsRemaining);
            if ($template) {
                $slot = new InstanceSlot();
                $slot->setSlot($i);
                $slot->setInstance($instance);
                $slot->setTemplate($template);
                $collection->add($slot);

                $templateContentSlots = $template->getDriver()->getTotalContentSlots();
                $totalItemsRemaining -= $templateContentSlots;
            }
            if ($totalItemsRemaining <= 0) {
                break;
            }
        }
        
        return $collection;
    }

}
