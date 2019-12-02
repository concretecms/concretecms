<?php
namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Entity\Board\Instance;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Entity\Board\Item;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Doctrine\Common\Collections\ArrayCollection;

class ContentPopulator
{

    /**
     * @var JsonSerializer 
     */
    protected $serializer;
    
    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Item[] $items
     * @return Item
     */
    protected function getItemFromPool(array $items)
    {
        $item = array_shift($items);
        return $item;
    }
    
    /**
     * @param Instance $instance
     * @param ArrayCollection $slots
     * @param Item[] $items
     */
    public function populateInstanceSlotContent(
        Instance $instance, 
        ArrayCollection $instanceSlots, 
        array $items
    ) : void
    {
        $type = BlockType::getByHandle(BLOCK_HANDLE_BOARD_SLOT_PROXY);
        /**
         * @var $instanceSlots InstanceSlot[]
         */
        foreach ($instanceSlots as $instanceSlot) {
            
            $contentObjectCollection = new ObjectCollection();
            $contentSlots = $instanceSlot->getTemplate()->getDriver()->getTotalContentSlots();
            for ($i = 1; $i <= $contentSlots; $i++) {
                $item = $this->getItemFromPool($items);
                if ($item) {
                    $driver = $item->getDataSource()->getDataSource()->getDriver();
                    $contentPopulator = $driver->getContentPopulator();
                    $data = $this->serializer->denormalize($item->getData(), $contentPopulator->getDataClass(), 'json');
                    $content = $driver->getContentPopulator()->createContentObject($data);
                    $contentObjectCollection->addContentObject($i, $content);
                }
            }
            
            // Now that we have an object collection, let's serialize it and to our core_board_slot object.
            $json = $this->serializer->serialize($contentObjectCollection, 'json');
            $block = $type->add([
                'contentObjectCollection' => $json,
                'slotTemplateID' => $instanceSlot->getTemplate()->getId(),
            ]);
            
            if ($block) {
                $instanceSlot->setBlockID($block->getBlockID());                
            }
        }
    }

}
