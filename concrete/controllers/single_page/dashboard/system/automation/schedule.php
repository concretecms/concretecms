<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Command\Process\Command\DeleteProcessCommand;
use Concrete\Core\Command\Process\Command\DeleteScheduledTaskCommand;
use Concrete\Core\Command\Scheduler\Scheduler;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Entity\Command\ScheduledTask;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\HttpFoundation\JsonResponse;

class Schedule extends DashboardPageController
{
    public function view()
    {
        $r = $this->entityManager->getRepository(ScheduledTask::class);
        $this->set('scheduledTasks', $r->findBy([], ['dateScheduled' => 'desc']));
        $this->set('enabled', $this->app->make(Scheduler::class)->isEnabled());
    }

    public function delete($token = null)
    {
        $scheduledTask = $this->entityManager->find(
            ScheduledTask::class,
            $this->request->request->get('scheduledTaskId')
        );
        if (!$this->token->validate('delete', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$scheduledTask) {
            $this->error->add(t('Invalid scheduled task ID'));
        }
        if (!$this->error->has()) {
            $this->executeCommand(new DeleteScheduledTaskCommand($scheduledTask->getID()));
            return new JsonResponse($scheduledTask);
        }

        return new JsonResponse($this->error);
    }

    public function update($token = null)
    {
        $scheduledTask = $this->entityManager->find(
            ScheduledTask::class,
            $this->request->request->get('scheduledTaskId')
        );
        if (!$this->token->validate('update', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$scheduledTask) {
            $this->error->add(t('Invalid scheduled task ID'));
        }
        $cronExpression = $this->request->request->get('cronExpression');
        if (!is_string($cronExpression) || trim($cronExpression) === '') {
            $this->error->add(t('You must specify a cron expression.'));
        }
        if (!$this->error->has()) {
            try {
                $notes = $this->request->request->get('notes');
                $this->app->make(Scheduler::class)->updateScheduledTask(
                    $scheduledTask,
                    trim($cronExpression),
                    is_string($notes) ? $notes : null
                );

                return new JsonResponse($scheduledTask);
            } catch (UserMessageException $e) {
                $this->error->add($e->getMessage());
            }
        }

        return new JsonResponse($this->error);
    }

}
