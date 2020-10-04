<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Entity\Board\Designer\CustomElementItem;
use Concrete\Core\Entity\Board\Designer\ItemSelectorCustomElementItem;
use Doctrine\ORM\EntityManager;

class SetItemSelectorCustomElementItemsCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(SetItemSelectorCustomElementItemsCommand $command)
    {

        $element = $command->getElement();
        $items = $element->getItems();
        foreach($items as $item) {
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();

        foreach($command->getItems() as $item) {
            $elementItem = new ItemSelectorCustomElementItem();
            $elementItem->setElement($command->getElement());
            $elementItem->setItem($item);
            $this->entityManager->persist($elementItem);
        }

        $this->entityManager->flush();
    }

    
}
