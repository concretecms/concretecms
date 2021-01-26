<?php

namespace Concrete\Controller\Dialog\Process;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Notification\Mercure\MercureService;
use Doctrine\ORM\EntityManager;

class Activity extends BackendInterfaceController
{

    protected $viewPath = '/dialogs/process/activity';

    public function view()
    {
        $token = $this->app->make('token');
        $processes = $this->app->make(EntityManager::class)->getRepository(Process::class)->findRunning();
        $mercureService = $this->app->make(MercureService::class);
        $eventSource = null;
        $poll = false;
        if ($mercureService->isEnabled()) {
            $eventSource = $mercureService->getPublisherUrl();
        } else {
            $poll = true;
        }
        $this->set('poll', $poll);
        $this->set('pollToken', $token->generate('poll'));
        $this->set('eventSource', $eventSource);
        $this->set('runningProcesses', $processes);
    }

    protected function canAccess()
    {
        $token = $this->app->make('token');
        return $token->validate('view_activity', $this->request->attributes->get('viewToken'));
    }

}
