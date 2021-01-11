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
        $consumeMethod = $this->app->make('config')->get('concrete.messenger.consume.method');
        $mercureService = $this->app->make(MercureService::class);
        $eventSource = null;
        if ($mercureService->isEnabled()) {
            $eventSource = $mercureService->getPublisherUrl();
        }
        $this->set('consume', $consumeMethod === 'app' ? true : false);
        $this->set('eventSource', $eventSource);
        $this->set('consumeToken', $token->generate('consume_messages'));
        $this->set('runningProcesses', $processes);
    }

    protected function canAccess()
    {
        $token = $this->app->make('token');
        return $token->validate('view_activity', $this->request->attributes->get('viewToken'));
    }

}
