<?php

namespace Concrete\Core\Board\Command;

use Doctrine\ORM\EntityManager;

class ClearBoardInstanceLogCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(ClearBoardInstanceLogCommand $command)
    {
        $instance = $command->getInstance();
        $log = $instance->getLog();
        if ($log) {
            $this->entityManager->remove($log);
            $this->entityManager->flush();
            $instance->setLog(null);
        }
    }


}
