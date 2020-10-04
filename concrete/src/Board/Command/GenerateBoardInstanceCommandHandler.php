<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Board\Instance\ItemSegmenter;
use Concrete\Core\Board\Instance\Slot\CollectionFactory;
use Concrete\Core\Board\Instance\Slot\Content\ContentPopulator;
use Concrete\Core\Board\Instance\Slot\SlotPopulator;
use Doctrine\ORM\EntityManager;

class GenerateBoardInstanceCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ContentPopulator
     */
    protected $contentPopulator;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SlotPopulator
     */
    protected $slotPopulator;

    /**
     * @var ItemSegmenter
     */
    protected $itemSegmenter;

    public function __construct(
        EntityManager $entityManager,
        ItemSegmenter $itemSegmenter,
        ContentPopulator $contentPopulator,
        SlotPopulator $slotPopulator,
        CollectionFactory $collectionFactory)
    {
        $this->entityManager = $entityManager;
        $this->itemSegmenter = $itemSegmenter;
        $this->contentPopulator = $contentPopulator;
        $this->collectionFactory = $collectionFactory;
        $this->slotPopulator = $slotPopulator;
    }

    public function __invoke(GenerateBoardInstanceCommand $command)
    {
        $instance = $command->getInstance();
        $items = $this->itemSegmenter->getBoardItemsForInstance($instance);
        if (count($items)) {
            $contentObjectGroups = $this->contentPopulator->createContentObjects($items);

            $collection = $this->collectionFactory->createSlotCollection($instance, $contentObjectGroups);

            // Now, however large our collection is, we need to move any existing content within our
            // instance DOWN by that many slots.
            $increment = (int) $collection->count();
            $db = $this->entityManager->getConnection();
            $db->executeQuery("update BoardInstanceSlots set slot = slot + {$increment}");

            // Now loop through our collection and ensure it's persisted.
            foreach ($collection as $slot) {
                $this->entityManager->persist($slot);
            }
            $this->entityManager->flush(); // need to do this here so our instance slots have IDs.

            $this->slotPopulator->populateSlotCollectionWithContent($contentObjectGroups, $collection);

            // Next, mark all items in our objectgroups as added to the board
            $items = [];
            foreach($contentObjectGroups as $contentObjectGroup) {
                if (!in_array($contentObjectGroup->getItem(), $items)) {
                    $items[] = $contentObjectGroup->getItem();
                }
            }
            $dateAddedToBoard = time();
            foreach($items as $item) {
                $item->setDateAddedToBoard($dateAddedToBoard);
                $this->entityManager->persist($slot);
            }

            $this->entityManager->flush(); // need to do this here so our instance slots have IDs.
        }
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
    }


}
