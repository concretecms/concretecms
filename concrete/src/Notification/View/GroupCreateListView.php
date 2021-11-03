<?php

namespace Concrete\Core\Notification\View;

use Concrete\Core\Entity\Notification\GroupCreateNotification;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\Group\Group;
use HtmlObject\Element;

class GroupCreateListView extends StandardListView
{

    /**
     * @var GroupCreateNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('Group creations');
    }

    public function getIconClass()
    {
        return 'fas fa-user';
    }

    public function getActionDescription()
    {
        $app = Facade::getFacadeApplication();
        $resolver = $app->make('url/manager');

        $user = $this->notification->getCreate()->getUser();
        $group = $this->notification->getCreate()->getGroup();

        return t('User <a href="%s">%s</a> created group %s.', $resolver->resolve([$user->getUserInfoObject()]), $user->getUserName(), $group->getGroupName());
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Created By '));
    }

}
