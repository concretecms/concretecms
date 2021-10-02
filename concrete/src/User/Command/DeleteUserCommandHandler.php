<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\User\UserInfoRepository;

class DeleteUserCommandHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    /** @var UserInfoRepository */
    protected $repository;

    /**
     * @param UserInfoRepository $repository
     */
    public function __construct(UserInfoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param DeleteUserCommand $command
     */
    public function __invoke(DeleteUserCommand $command)
    {
        $this->output->write(t('Deleting user ID: %s', $command->getUserID()));
        $user = $this->repository->getByID($command->getUserID());
        if ($user) {
            if ($user->delete() === false) {
                $this->output->write(t('Deleting user %s has been canceled...', $command->getUserID()));
            }
        } else {
            $this->output->write(t('Userinfo object for ID %s not found. Skipping...', $command->getUserID()));
        }
    }
}