<?php

namespace Concrete\Core\Command\Task;

use Concrete\Core\Entity\Attribute\Set as SetEntity;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Entity\Automation\TaskSet;
use Concrete\Core\Entity\Automation\TaskSetTask;
use Concrete\Core\Entity\Package;
use Concrete\Core\Entity\User\User as UserEntity;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;
use Punic\Comparer;

/**
 * Methods useful for working with Task sets.
 */
class TaskSetService
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var TaskService
     */
    protected $taskService;

    /**
     * TaskService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager,
        TaskService $taskService
    ) {
        $this->entityManager = $entityManager;
        $this->taskService = $taskService;
    }

    public function getList()
    {
        $sets = $this->entityManager->getRepository(TaskSet::class)
            ->findBy([], ['displayOrder' => 'asc']);
        return $sets;
    }

    public function getByHandle(string $taskSetHandle)
    {
        $r = $this->entityManager->getRepository(TaskSet::class);
        return $r->findOneByHandle($taskSetHandle);
    }

    public function add(string $handle, string $name, Package $pkg = null)
    {
        $displayOrder = 0;
        $sets = $this->getList();
        if (count($sets) > 0) {
            $displayOrder = count($sets);
        }

        $set = new TaskSet();
        $set->setHandle($handle);
        $set->setName($name);
        $set->setDisplayOrder($displayOrder);
        if ($pkg) {
            $set->setPackage($pkg);
        }
        $this->entityManager->persist($set);
        $this->entityManager->flush();
        return $set;
    }

    public function taskSetContainsTask(TaskSet $set, Task $task): bool
    {
        foreach($set->getTasks() as $taskSetTask) {
            if ($taskSetTask->getID() == $task->getID()) {
                return true;
            }
        }
        return false;
    }

    public function addTaskToSet(Task $task, TaskSet $set): void
    {
        $displayOrder = 0;
        $tasks = $set->getTasks();
        if (count($tasks) > 0) {
            $displayOrder = count($tasks);
        }

        $r = $this->entityManager->getRepository(TaskSetTask::class);
        $taskSetTask = $r->findOneBy(array('task' => $task, 'set' => $set));
        if (!is_object($taskSetTask)) {
            $taskSetTask = new TaskSetTask();
            $taskSetTask->setTask($task);
            $taskSetTask->setTaskSet($set);
            $taskSetTask->setDisplayOrder($displayOrder);
            $set->getTaskCollection()->add($taskSetTask);
            $this->entityManager->persist($taskSetTask);
            $this->entityManager->flush();
        }
    }

    /**
     * Used in the tasks dashboard page, this returns a grouped set of tasks. If any tasks are not in a set
     * they are added in a final set named 'Other'
     *
     * @return TaskSet[]
     */
    public function getGroupedTasks(): array
    {
        $sets = $this->getList();
        $allTasks = $this->taskService->getList();

        // Now check to see if anything is unassigned.
        $unassignedTasks = [];
        foreach($allTasks as $task) {
            $query = $this->entityManager->createQuery(
                'select tst from \Concrete\Core\Entity\Automation\TaskSetTask tst where tst.task = :task'
            );
            $query->setParameter('task', $task);
            $query->setMaxResults(1);
            $r = $query->getOneOrNullResult();
            if (!is_object($r)) {
                $unassignedTasks[] = $task;
            }
        }

        if (count($unassignedTasks) > 0) {
            $unassignedSet = new TaskSet();
            $unassignedSet->setName(t('Other'));
            foreach($unassignedTasks as $unassignedTask) {
                $unassignedTaskSetTask = new TaskSetTask();
                $unassignedTaskSetTask->setTask($unassignedTask);
                $unassignedTaskSetTask->setTaskSet($unassignedSet); // probably not necessary
                $unassignedSet->getTaskCollection()->add($unassignedTaskSetTask);
            }
            $sets[] = $unassignedSet;
        }
        return $sets;
    }
}
