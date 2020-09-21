<?php

namespace Concrete\Core\Automation\Task;

use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;

/**
 * Methods useful for working with Task objects.
 */
class TaskService
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var UserInfoRepository
     */
    protected $userInfoRepository;

    /**
     * @var Date
     */
    protected $dateService;

    /**
     * TaskService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(Date $dateService, User $user, UserInfoRepository $userInfoRepository, EntityManager $entityManager)
    {
        $this->dateService = $dateService;
        $this->user = $user;
        $this->userInfoRepository = $userInfoRepository;
        $this->entityManager = $entityManager;
    }

    public function start(Task $task)
    {
        $lastRunBy = null;
        if ($this->user->isRegistered()) {
            $userInfo = $this->userInfoRepository->getByID($this->user->getUserID());
            $lastRunBy = $userInfo->getEntityObject();
        }
        $task->setLastRunBy($lastRunBy);
        $task->setDateLastStarted($this->dateService->toDateTime()->getTimestamp());
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function complete(Task $task)
    {
        $task->setDateLastCompleted($this->dateService->toDateTime()->getTimestamp());
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }



}
