<?php

namespace Concrete\Core\Command\Task;

use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;
use Punic\Comparer;

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
    public function __construct(
        Date $dateService,
        User $user,
        UserInfoRepository $userInfoRepository,
        EntityManager $entityManager
    ) {
        $this->dateService = $dateService;
        $this->user = $user;
        $this->userInfoRepository = $userInfoRepository;
        $this->entityManager = $entityManager;
    }

    public function getList()
    {
        $tasks = $this->entityManager->getRepository(Task::class)
            ->findAll();
        $comparer = new Comparer();
        usort($tasks, function($a, $b) use ($comparer) {
            return $comparer->compare($a->getController()->getName(), $b->getController()->getName());
        });
        return $tasks;
    }

    protected function getCurrentUserEntity(): ?UserEntity
    {
        if ($this->user->isRegistered()) {
            $userInfo = $this->userInfoRepository->getByID($this->user->getUserID());
            return $userInfo->getEntityObject();
        }
        return null;
    }

    public function start(Task $task)
    {
        $task->setLastRunBy($this->getCurrentUserEntity());
        $task->setDateLastStarted($this->dateService->toDateTime()->getTimestamp());
        $task->setDateLastCompleted(null);
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
