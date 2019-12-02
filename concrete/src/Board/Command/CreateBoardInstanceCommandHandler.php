<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Board\Instance\Slot\CollectionFactory;
use Concrete\Core\Board\Instance\Slot\ContentPopulator;
use Concrete\Core\Board\Instance\ItemSegmenter;
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
    
    public function __construct(
        EntityManager $entityManager, 
        CollectionFactory $collectionFactory,
        ContentPopulator $contentPopulator,
        ItemSegmenter $itemSegmenter)
    {
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
        $this->contentPopulator = $contentPopulator;
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
        
        // First, let's create board instance slots for all the board slots in this board template
        $collection = $this->collectionFactory->createSlotCollection($instance);

        // Now that we have slots, let's pick a subset of our data pool to populate in these slots
        $items = $this->itemSegmenter->getBoardItemsForInstance($instance, $collection);

        // Now we have the items we're going to use to build our content objects.
        $this->contentPopulator->populateInstanceSlotContent($instance, $collection, $items);

        // Now save the board instance.
        $instance->setSlots($collection);
        $this->entityManager->persist($instance);
        $this->entityManager->flush();
        
        
        
    }

    
}
