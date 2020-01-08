<?php

namespace Concrete\Core\Page\Container\Command;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Foundation\Command\CommandInterface;
use Doctrine\ORM\EntityManager;

class DeleteContainerCommandHandler
{
    
    /**
     * @var EntityManager 
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ContainerCommand $command)
    {
        $container = $command->getContainer();
        $this->entityManager->remove($container);
        $this->entityManager->flush();
    }

    
}
