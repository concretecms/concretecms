<?php

namespace Concrete\Controller\Dialog\Process;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Notification\Events\Traits\SubscribeToProcessTopicsTrait;
use Doctrine\ORM\EntityManager;

class Activity extends BackendInterfaceController
{

    use SubscribeToProcessTopicsTrait;

    protected $viewPath = '/dialogs/process/activity';

    protected function canAccess()
    {
        $token = $this->app->make('token');
        return $token->validate('view_activity', $this->request->attributes->get('viewToken'));
    }

    public function view()
    {
        $processes = $this->app->make(EntityManager::class)->getRepository(Process::class)->findRunning();
        $this->set('processes', $processes);
        $this->subscribeToProcessTopicsIfNotificationEnabled();
    }

}
