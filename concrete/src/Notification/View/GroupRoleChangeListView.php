<?php

namespace Concrete\Core\Notification\View;

use Concrete\Core\Entity\Notification\GroupRoleChangeNotification;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupRole;
use HtmlObject\Element;

class GroupRoleChangeListView extends StandardListView
{

    /**
     * @var GroupRoleChangeNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('Group role changes');
    }

    public function getIconClass()
    {
        return 'fas fa-user';
    }

    public function getActionDescription()
    {
        $app = Facade::getFacadeApplication();
        $resolver = $app->make('url/manager');

        $user = $this->notification->getGroupRoleChange()->getUser();

        $user = $this->notification->getGroupRoleChange()->getUser();
        $group = $this->notification->getGroupRoleChange()->getGroup();
        $role = $this->notification->getGroupRoleChange()->getRole();

        return t('User <a href="%s">%s</a> of group %s changed role to %s.', $resolver->resolve([$user->getUserInfoObject()]), $user->getUserName(), $group->getGroupName(), $role->getName());
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Created By '));
    }

}
