<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\SlotTemplate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class EnableCustomSlotTemplatesCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(
        EnableCustomSlotTemplatesCommand $command)
    {

        $board = $command->getBoard();
        $this->clearBoardTemplatesCollection($board);
        $board->setHasCustomSlotTemplates(true);
        $this->entityManager->persist($board);
        $this->entityManager->flush();

        $collection = $board->getCustomSlotTemplates();
        $templateIDs = $command->getTemplateIDs();

        if (!empty($templateIDs)) {
            foreach($templateIDs as $templateID) {
                $template = $this->entityManager->find(SlotTemplate::class,
                    $templateID
                );
                if ($template) {
                    $collection->add($template);
                }
            }
        }

        $this->entityManager->flush();
    }
    
    protected function clearBoardTemplatesCollection(Board $board)
    {
        $board->setCustomSlotTemplates(new ArrayCollection());
        $this->entityManager->persist($board);
        $this->entityManager->flush();
    }

}
