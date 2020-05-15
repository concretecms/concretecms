<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Board\Instance\Slot\CollectionFactory;
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

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
        $this->entityManager = $entityManager;
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
        $instance->setBoardInstanceName($command->getBoardInstanceName());
        $instance->setSite($command->getSite());
        $instance->setDateCreated($this->createInstanceDateTime($board)->getTimestamp());
        $this->entityManager->persist($instance);
        $this->entityManager->flush();

    }


}
