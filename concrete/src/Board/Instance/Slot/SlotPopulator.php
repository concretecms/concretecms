<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Board\Instance\Slot\Content\ItemObjectGroup;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Debug\Dumper;

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
     * @param ItemObjectGroup[] $contentObjects
     * @param InstanceSlot[] $instanceSlots
     */
    public function populateSlotCollectionWithContent(array $contentObjectGroups, ArrayCollection $instanceSlots)
    {
        

        $type = BlockType::getByHandle(BLOCK_HANDLE_BOARD_SLOT_PROXY);
        
        foreach($instanceSlots as $instanceSlot) {
            shuffle($contentObjectGroups);

            $templateDriver = $instanceSlot->getTemplate()->getDriver();
            $contentSlots = $templateDriver->getTotalContentSlots();

            $objectGroups = array_splice($contentObjectGroups, 0, $contentSlots);

            // object groups contains all the groups for our slot.
            $contentObjectCollection = new ObjectCollection();

            for ($i = 1; $i <= $contentSlots; $i++) {

                $contentSlotObjectGroups = $objectGroups[$i - 1];
                if (!$contentSlotObjectGroups) {
                    foreach($instanceSlots as $instanceSlot) {

                        $templateDriver = $instanceSlot->getTemplate()->getDriver();
                        $contentSlots = $templateDriver->getTotalContentSlots();

                        Dumper::dump($instanceSlot->getTemplate());
                    }
                    exit;
                }
                $contentObjects = $contentSlotObjectGroups->getContentObjects();

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

