<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;

class ReindexPageTaskCommandHandler extends ReindexPageCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @param ReindexPageTaskCommand $command
     */
    public function __invoke($command)
    {
        $this->output->write(t('Reindexing page ID: %s', $command->getPageID()));
        parent::__invoke($command);
    }


}