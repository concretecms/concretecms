<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Block\Block;
use Concrete\Core\Board\Instance\Slot\CollectionFactory;
use Concrete\Core\Board\Instance\Slot\Content\ObjectCollection;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Doctrine\ORM\EntityManager;

class RegenerateBoardInstanceCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(EntityManager $entityManager, CollectionFactory $collectionFactory)
    {
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
    }

    public function handle(RegenerateBoardInstanceCommand $command)
    {
        $instance = $command->getInstance();
        $slots = $instance->getSlots();
        foreach($slots as $slot) {
            $this->entityManager->remove($slot);
        }
        $this->entityManager->flush();

        $collection = $this->collectionFactory->createSlotCollection($instance);
        $instance->setSlots($collection);
        $instance->setDateContentLastAddedToInstance(time());
        $this->entityManager->persist($instance);
        $this->entityManager->flush();

    }


}
