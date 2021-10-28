<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\User\UserInfoRepository;

class DeleteUserCommandHandler
{
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
        $user = $this->repository->getByID($command->getUserID());
        if ($user) {
            $user->delete();
        }
    }
}
