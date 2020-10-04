<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\SlotTemplate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class DisableCustomSlotTemplatesCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    protected function clearBoardTemplatesCollection(Board $board)
    {
        $board->setCustomSlotTemplates(new ArrayCollection());
        $this->entityManager->persist($board);
        $this->entityManager->flush();
    }

    public function __invoke(
        DisableCustomSlotTemplatesCommand $command)
    {
        $board = $command->getBoard();
        $this->clearBoardTemplatesCollection($board);
        $board->setHasCustomSlotTemplates(false);
        $this->entityManager->persist($board);
        $this->entityManager->flush();
    }    
}
