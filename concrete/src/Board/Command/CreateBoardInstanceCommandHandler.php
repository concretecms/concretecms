<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Board\Instance\Slot\CollectionFactory;
use Concrete\Core\Board\Instance\Slot\Content\ContentPopulator;
use Concrete\Core\Board\Instance\ItemSegmenter;
use Concrete\Core\Board\Instance\Slot\SlotPopulator;
use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Instance;
use Doctrine\ORM\EntityManager;

class CreateBoardInstanceCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var CollectionFactory 
     */
    protected $collectionFactory;

    /**
     * @var ContentPopulator 
     */
    protected $contentPopulator;

    /**
     * @var ItemSegmenter
     */
    protected $itemSegmenter;

    /**
     * @var SlotPopulator
     */
    protected $slotPopulator;

    public function __construct(
        EntityManager $entityManager, 
        CollectionFactory $collectionFactory,
        ContentPopulator $contentPopulator,
        SlotPopulator $slotPopulator,
        ItemSegmenter $itemSegmenter)
    {
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
        $this->contentPopulator = $contentPopulator;
        $this->slotPopulator = $slotPopulator;
        $this->itemSegmenter = $itemSegmenter;
    }

    protected function createInstanceDateTime(Board $board)
    {
        $site = $board->getSite();
        $dateTime = new \DateTime();
        if ($site) {
            $dateTime->setTimezone(new \DateTimeZone($site->getTimezone()));
        }
        return $dateTime;
    }

    
    public function handle(CreateBoardInstanceCommand $command)
    {
        $board = $command->getBoard();
        $instance = new Instance();
        $instance->setBoard($board);
        $instance->setDateCreated($this->createInstanceDateTime($board)->getTimestamp());
        
        // Let's create items from our data sources to put into our board.
        $items = $this->itemSegmenter->getBoardItemsForInstance($instance);

        // Now that we have items, let's create a pool of content objects.
        $contentObjectGroups = $this->contentPopulator->createContentObjects($items);
        
        // Now, let's create the outer slot objects for our board. We'll use the content object groups
        // to determine the layout of our board, how many slots it can support, etc
        $collection = $this->collectionFactory->createSlotCollection($instance, $contentObjectGroups);
        
        $this->slotPopulator->populateSlotCollectionWithContent($contentObjectGroups, $collection);

        // Now save the board instance.
        $instance->setSlots($collection);
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
        
    }

    
}
