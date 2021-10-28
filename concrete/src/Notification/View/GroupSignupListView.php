<?php

namespace Concrete\Core\Notification\View;

use Concrete\Core\Entity\Notification\GroupSignupNotification;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Group\Group;
use HtmlObject\Element;

class GroupSignupListView extends StandardListView
{

    /**
     * @var GroupSignupNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('Group signups');
    }

    public function getIconClass()
    {
        return 'fas fa-user';
    }

    public function getActionDescription()
    {
        $app = Facade::getFacadeApplication();
        $resolver = $app->make('url/manager');

        $user = $this->notification->getSignup()->getUser();
        $group = $this->notification->getSignup()->getGroup();

        return t('User <a href="%s">%s</a> signup to group %s.', $resolver->resolve([$user->getUserInfoObject()]), $user->getUserName(), $group->getGroupName());
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Created By '));
    }

}
