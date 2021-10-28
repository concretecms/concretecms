<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Application\Application;

class RegenerateBoardInstanceCommandHandler
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Clears out the board data pool, repopulates it, clears the instance of the board and regenerates it.
     * @param RegenerateBoardInstanceCommand $command
     */
    public function __invoke(RegenerateBoardInstanceCommand $command)
    {
        $instance = $command->getInstance();
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
