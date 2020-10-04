<?php

namespace Concrete\Core\Board\Designer\Command;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Designer\CustomElement;
use Concrete\Core\Entity\Board\Designer\ItemSelectorCustomElement;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;

class CreateItemSelectorCustomElementCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var UuidGenerator
     */
    protected $uuidGenerator;

    public function __construct(UuidGenerator $uuidGenerator, User $user, EntityManager $entityManager)
    {
        $this->user = $user;
        $this->uuidGenerator = $uuidGenerator;
        $this->entityManager = $entityManager;
    }

    public function __invoke(CreateItemSelectorCustomElementCommand $command)
    {
        if ($this->user->isRegistered()) {
            $author = $this->user->getUserInfoObject()->getEntityObject();
        }

        $element = new ItemSelectorCustomElement();
        $element->setElementName($command->getElementName());
        $element->setDateCreated(time());
        $element->setAuthor($author);
        $element->setBatchIdentifier($this->uuidGenerator->generate($this->entityManager, $element));

        $this->entityManager->persist($element);
        $this->entityManager->flush();
        
        return $element;
    }

    
}
