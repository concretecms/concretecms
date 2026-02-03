<?php

namespace Concrete\Core\Board\Command;

class RegenerateRelevantBoardInstancesCommandHandler extends AbstractUpdateBoardInstanceCommandHandler
{
    public function __invoke(RegenerateRelevantBoardInstancesCommand $command)
    {
        if ($this->performUpdate()) {
            $this->logger->debug(t('Automatic board instance regeneration triggered.'));
            foreach ($this->getInstances($command) as $instance) {
                $regenerateCommand = new RegenerateBoardInstanceCommand();
                $regenerateCommand->setDefer(true);
                $regenerateCommand->setInstance($instance);
                $this->app->executeCommand($regenerateCommand);
            }
        } else {
            $this->logger->debug(t('Automatic board instance regeneration requested but skipped due to configuration.'));
        }
    }

}
