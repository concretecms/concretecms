<?php
namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Entity\Board\SlotTemplate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Board\Instance\Slot\ContentSlot\CollectionFactory as ContentSlotCollectionFactory;
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

    /**
     * @var ContentSlotCollectionFactory 
     */
    protected $collectionFactory;
    
    public function __construct(EntityManager $entityManager, ContentSlotCollectionFactory $collectionFactory)
    {
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
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

            $contentSlotCollection = $this->collectionFactory->createContentSlotCollection($slot);
            $slot->setContentSlots($contentSlotCollection);

            $collection->add($slot);
        }
        return $collection;
    }

}
