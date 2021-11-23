<?php

namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Board\Item\ItemProviderInterface;
use Concrete\Core\Entity\Board\SlotTemplate;

class AvailableObjectCollectionFactory
{

    protected function generateCombinations(array $arrays, $i = 0)
    {
        if (!isset($arrays[$i])) {
            return [];
        }
        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }

        // get combinations from subsequent arrays
        $tmp = $this->generateCombinations($arrays, $i + 1);
        $result = [];

        foreach ($arrays[$i] as $v) {
            foreach ($tmp as $t) {
                $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
            }
        }

        // Now we remove any rows where that are duplicate itemIDs
        $result = array_filter($result, function($values) {
            $itemIDs = [];
            foreach ($values as $availableObject) {
                /**
                 * @var $availableObject AvailableObject
                 */
                $itemIDs[] = $availableObject->getItem()->getItem()->getUniqueItemId();
            }
            $countedValues = array_count_values($itemIDs);
            foreach($countedValues as $countedValue) {
                if ($countedValue > 1) {
                    return false;
                }
            }
            return true;
        });

        return $result;
    }

    /**
     * Given a particular slot template and an array of ItemObjectGroup objects, return all possible
     * ObjectCollections for admin editors to choose from.
     *
     * @param SlotTemplate $slotTemplate
     * @param ItemObjectGroup[] $itemObjectGroups
     * @return ObjectCollection[]
     */
    public function getObjectCollectionsForTemplate(SlotTemplate $template, array $itemObjectGroups)
    {
        $filterer = $template->getDriver()->getSlotFilterer();
        $templateDriver = $template->getDriver();
        $contentSlots = $templateDriver->getTotalContentSlots();

        $slots = [];
        for ($i = 0; $i < $contentSlots; $i++) {
            $availableObjects = [];
            foreach ($itemObjectGroups as $itemObjectGroup) {
                /**
                 * @var $itemObjectGroup ItemObjectGroup
                 */
                $item = $itemObjectGroup->getItem();
                $objects = $itemObjectGroup->getContentObjects();
                if ($filterer) {
                    $objects = $filterer->findContentObjectsForSlot($objects, $i + 1);
                }

                if ($objects) {
                    foreach ($objects as $contentObject) {
                        $availableObjects[] = new AvailableObject($i + 1, $item, $contentObject);
                    }
                }
            }
            $slots[$i] = $availableObjects;
        }

        if ($contentSlots > 1) {
            $combinations = $this->generateCombinations($slots);
            foreach($combinations as $combination) {
                $collection = new ObjectCollection();
                foreach($combination as $slot => $availableObject) {
                    $collection->addContentObject($slot + 1, $availableObject->getContentObject());
                }
                $collections[] = $collection;
            }
        } else {
            foreach($slots[0] as $availableObject) {
                $collection = new ObjectCollection();
                $collection->addContentObject(1, $availableObject->getContentObject());
                $collections[] = $collection;
            }
        }

        return $collections;
    }

}
