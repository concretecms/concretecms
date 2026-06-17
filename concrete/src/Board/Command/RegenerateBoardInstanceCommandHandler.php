<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Application\Application;
use Doctrine\ORM\EntityManager;

class RegenerateBoardInstanceCommandHandler
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(Application $app, EntityManager $entityManager)
    {
        $this->app = $app;
        $this->entityManager = $entityManager;
    }

    /**
     * Clears out the board data pool, repopulates it, clears the instance of the board and regenerates it.
     * @param RegenerateBoardInstanceCommand $command
     */
    public function __invoke(RegenerateBoardInstanceCommand $command)
    {
        $instance = $command->getInstance();
        if ($command->isDefer()) {
            if ($instance->isGenerating()) {
                // We will NOT allow you to attempt to regenerate the board multiple times in a row.
                return false;
            }
            $instance->setIsGenerating(true);
            $instance->setDateLastGenerated(time());
            $this->entityManager->persist($instance);
            $this->entityManager->flush();
            $regenerateCommand = new RegenerateBoardInstanceAsyncCommand();
            $regenerateCommand->setInstance($instance);
            return $this->app->executeCommand($regenerateCommand);
        } else {
            $command = new ClearBoardInstanceLogCommand();
            $command->setInstance($instance);
            $this->app->executeCommand($command);

            $command = new ClearBoardInstanceDataPoolCommand();
            $command->setInstance($instance);
            $this->app->executeCommand($command);

            $command = new PopulateBoardInstanceDataPoolCommand();
            $command->setInstance($instance);
            $this->app->executeCommand($command);

            $command = new ClearBoardInstanceCommand();
            $command->setInstance($instance);
            $this->app->executeCommand($command);

            $command = new GenerateBoardInstanceCommand();
            $command->setInstance($instance);
            $this->app->executeCommand($command);
        }
    }
}
