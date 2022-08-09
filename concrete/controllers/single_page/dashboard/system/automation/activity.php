<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Command\Process\Command\DeleteProcessCommand;
use Concrete\Core\Command\Process\Logger\LoggerFactoryInterface;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Notification\Events\Traits\SubscribeToProcessTopicsTrait;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\HttpFoundation\JsonResponse;

class Activity extends DashboardPageController
{

    use SubscribeToProcessTopicsTrait;

    public function view($processID = null)
    {
        $r = $this->entityManager->getRepository(Process::class);
        $this->set('processes', $r->findBy([], ['dateCompleted' => 'desc']));
        $this->set('processID', $processID);
        $this->subscribeToProcessTopicsIfNotificationEnabled();
    }


    public function delete($token = null)
    {
        $process = $this->entityManager->find(
            Process::class,
            $this->request->request->get('processId')
        );
        if (!$this->token->validate('delete', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$process) {
            $this->error->add(t('Invalid process ID'));
        }
        if (!$this->error->has()) {
            $this->executeCommand(new DeleteProcessCommand($process->getID()));
            return new JsonResponse($process);
        }

        return new JsonResponse($this->error);
    }

    public function details($token = null)
    {
        $process = $this->entityManager->find(
            Process::class,
            $this->request->request->get('processId')
        );
        if (!$this->token->validate('details', $token)) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$process) {
            $this->error->add(t('Invalid process ID'));
        }
        if (!$this->error->has()) {
            $logger = $this->app->make(LoggerFactoryInterface::class)->createFromProcess($process);
            if ($logger) {
                if ($logger->logExists()) {
                    return new JsonResponse($logger->readAsArray());
                }
            }
        }

        return new JsonResponse([]);
    }


}
