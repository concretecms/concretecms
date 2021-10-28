<?php

namespace Concrete\Core\Notification\View;

use Concrete\Core\Entity\Notification\GroupSignupRequestDeclineNotification;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Group\Group;
use HtmlObject\Element;

class GroupSignupRequestDeclineListView extends StandardListView
{

    /**
     * @var GroupSignupRequestDeclineNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('Group declined signup requests');
    }

    public function getIconClass()
    {
        return 'fas fa-user';
    }

    public function getActionDescription()
    {
        $app = Facade::getFacadeApplication();
        $resolver = $app->make('url/manager');

        $user = $this->notification->getSignupRequestDecline()->getUser();
        $group = $this->notification->getSignupRequestDecline()->getGroup();
        $manager = $this->notification->getSignupRequestDecline()->getManager();

        return t('User <a href="%s">%s</a> declined the request to signup the group %s from user <a href="%s">%s</a>.',
            $resolver->resolve([$manager->getUserInfoObject()]),
            $manager->getUserName(),
            $group->getGroupName(),
            $resolver->resolve([$user->getUserInfoObject()]),
            $user->getUserName()
        );
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Created By '));
    }

}
