<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\User\UserInfoRepository;

class DeleteUserTaskCommandHandler extends DeleteUserCommandHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    /**
     * @param DeleteUserCommand $command
     */
    public function __invoke(DeleteUserCommand $command)
    {
        $this->output->write(t('Deleting user ID: %s', $command->getUserID()));
        parent::__invoke($command);
    }
}
