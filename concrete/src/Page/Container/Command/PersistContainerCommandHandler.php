<?php

namespace Concrete\Core\Page\Container\Command;

use Concrete\Core\Entity\Page\Container;
use Concrete\Core\Foundation\Command\CommandInterface;
use Doctrine\ORM\EntityManager;

class PersistContainerCommandHandler
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
        $this->entityManager->persist($container);
        $this->entityManager->flush();
        
        return $container;
    }

    
}
