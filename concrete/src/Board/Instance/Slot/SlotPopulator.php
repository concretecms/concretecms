<?php

namespace Concrete\Core\Board\Instance\Slot;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Board\Instance\Slot\Content\ItemObjectGroup;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Board\Instance\Slot\Content\ObjectInterface;
use Concrete\Core\Entity\Board\InstanceSlot;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Debug\Dumper;

class SlotPopulator implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        Channels::CHANNEL_CONTENT;
    }

    /**
     * @var JsonSerializer
     */
    protected $serializer;

    public function __construct(JsonSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param ItemObjectGroup[] $contentObjectGroups
     * @param InstanceSlot[] $instanceSlots
     */
    public function populateSlotCollectionWithContent(array $contentObjectGroups, ArrayCollection $instanceSlots)
    {

        $type = BlockType::getByHandle(BLOCK_HANDLE_BOARD_SLOT_PROXY);

        $this->logger->debug(t('Passed %s instance slots and %s content groups passed to populate function',
            count($instanceSlots), count($contentObjectGroups)));

        foreach($instanceSlots as $instanceSlot) {

            $templateDriver = $instanceSlot->getTemplate()->getDriver();
            $contentSlots = $templateDriver->getTotalContentSlots();

            $objectGroups = array_splice($contentObjectGroups, 0, $contentSlots);

            $this->logger->debug(t('%s content slots retrieved from template %s',
                $contentSlots, $instanceSlot->getTemplate()->getName()));

            $this->logger->debug(t('Content object groups decreased to %s', count($contentObjectGroups)));

            // object groups contains all the groups for our slot.
            $contentObjectCollection = new ObjectCollection();

            for ($i = 1; $i <= $contentSlots; $i++) {

                $contentSlotObjectGroups = $objectGroups[$i - 1];
                $contentObjects = $contentSlotObjectGroups->getContentObjects();

                $this->logger->debug(t(
                    '%s content objects retrieved from content object group for slot %s',
                    count($contentObjects), $i
                ));

                $filterer = $templateDriver->getSlotFilterer();
                if ($filterer) {
                    $objects = $filterer->findContentObjectsForSlot($contentObjects, $i);
                } else {
                    $objects = $contentObjects;
                }

                if (count($objects)) {
                    $content = $objects[array_rand($objects, 1)];
                    $contentObjectCollection->addContentObject($i, $content);
                    $this->logger->debug(t(
                        'Post slot filterer, populating content slot %s with content object %s', $i,
                        json_encode($content)
                    ));
                } else {

                    $this->logger->debug(t('No content objects found when attempting to populate slot %s',
                        $i));
                }
            }

            // Now that we have an object collection, let's serialize it and to our core_board_slot object.
            $json = $this->serializer->serialize($contentObjectCollection, 'json');

            $data = [
                'contentObjectCollection' => $json,
                'slotTemplateID' => $instanceSlot->getTemplate()->getId(),
            ];

            $block = $type->add($data);

            if ($block) {
                $instanceSlot->setBlockID($block->getBlockID());
            }

        }
    }

}

