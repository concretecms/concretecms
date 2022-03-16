<?php

namespace Concrete\Core\Notification\View;

use Concrete\Core\Entity\Notification\GroupSignupRequestNotification;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Group\Group;
use HtmlObject\Element;

class GroupSignupRequestListView extends StandardListView
{

    /**
     * @var GroupSignupRequestNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('Group signup requests');
    }

    public function getIconClass()
    {
        return 'fas fa-user';
    }

    public function getActionDescription()
    {
        $app = Facade::getFacadeApplication();
        $resolver = $app->make('url/manager');

        $user = $this->notification->getSignupRequest()->getUser();
        $group = $this->notification->getSignupRequest()->getGroup();

        return t('User <a href="%s">%s</a> send request to signup group %s.', $resolver->resolve([$user->getUserInfoObject()]), $user->getUserName(), $group->getGroupName());
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Created By '));
    }

}
