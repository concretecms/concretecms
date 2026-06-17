<?php

namespace Concrete\Controller\Dialog\Process;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Notification\Events\Traits\SubscribeToProcessTopicsTrait;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
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
        $page = Page::getByPath('/dashboard/system/automation/activity');
        $processes = $this->app->make(EntityManager::class)->getRepository(Process::class)->findRunning();
        $showManageActivityButton = false;
        if ($page && !$page->isError()) {
            $checker = new Checker($page);
            if ($checker->canViewPage()) {
                $showManageActivityButton = true;
            }
        }
        $this->set('processes', $processes);
        $this->set('showManageActivityButton', $showManageActivityButton);
        $this->subscribeToProcessTopicsIfNotificationEnabled();
    }

}
