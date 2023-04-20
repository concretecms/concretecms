<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Api\Command\SynchronizeScopesCommand;
use Concrete\Core\Application\Application;

class InstallApiRoutineHandler
{

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __invoke()
    {
        $command = new SynchronizeScopesCommand();
        $this->app->executeCommand($command);
    }


}
