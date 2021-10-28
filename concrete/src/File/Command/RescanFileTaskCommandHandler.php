<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;

class RescanFileTaskCommandHandler extends RescanFileCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @param RescanFileTaskCommand $command
     */
    public function __invoke(RescanFileCommand $command)
    {
        $this->output->write(t('Rescanning file ID: %s', $command->getFileID()));
        parent::__invoke($command);
    }


}