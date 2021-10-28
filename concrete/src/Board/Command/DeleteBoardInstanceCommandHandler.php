<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Entity\Board\Board;
use Concrete\Core\Entity\Board\Instance;
use Doctrine\ORM\EntityManager;

class DeleteBoardInstanceCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var DateTime
     */
    protected $now;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    
    public function __invoke(DeleteBoardInstanceCommand $command)
    {
        $this->entityManager->remove($command->getInstance());
        $this->entityManager->flush();
    }

    
}
