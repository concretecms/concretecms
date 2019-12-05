<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Doctrine\Common\Collections\ArrayCollection;

class SlotPopulator
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
     * @param ObjectInterface[] $contentObjects
     * @param InstanceSlot[] $instanceSlots
     */
    public function populateSlotCollectionWithContent(array $contentObjects, ArrayCollection $instanceSlots)
    {
        $type = BlockType::getByHandle(BLOCK_HANDLE_BOARD_SLOT_PROXY);
        
        foreach ($instanceSlots as $instanceSlot) {

            $templateDriver = $instanceSlot->getTemplate()->getDriver();
            $contentSlots = $templateDriver->getTotalContentSlots();
            $contentObjectCollection = new ObjectCollection();

            for ($i = 1; $i <= $contentSlots; $i++) {

                $filterer = $templateDriver->getSlotFilterer();
                if ($filterer) {
                    $objects = $filterer->findContentObjectsForSlot($contentObjects, $i);
                } else {
                    $objects = $contentObjects;
                }

                $content = $objects[array_rand($objects, 1)];
                $contentObjectCollection->addContentObject($i, $content);
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

