<?php

namespace Concrete\Core\Board\Command;

class RefreshRelevantBoardInstancesCommandHandler extends AbstractUpdateBoardInstanceCommandHandler
{
    public function __invoke(RefreshRelevantBoardInstancesCommand $command): void
    {
        if ($this->performUpdate()) {
            $this->logger->debug(t('Automatic board instance refresh triggered.'));
            foreach ($this->getInstances($command) as $instance) {
                $refreshCommand = new RefreshBoardInstanceCommand();
                $refreshCommand->setInstance($instance);
                $this->app->executeCommand($refreshCommand);
            }
        } else {
            $this->logger->debug(t('Automatic board instance refresh requested but skipped due to configuration.'));
        }
    }
    

}
