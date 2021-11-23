<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Board\InstanceItemBatch;
use Doctrine\ORM\EntityManager;

class AddContentToBoardInstanceCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(EntityManager $entityManager, Application $app)
    {
        $this->entityManager = $entityManager;
        $this->app = $app;
    }

    public function __invoke(AddContentToBoardInstanceCommand $command)
    {
        $instance = $command->getInstance();

        $populate = new PopulateBoardInstanceDataPoolCommand();
        $populate->setInstance($instance);
        $this->app->executeCommand($populate);

        $generate = new GenerateBoardInstanceCommand();
        $generate->setInstance($instance);
        $this->app->executeCommand($generate);
    }


}
