<?php
namespace Concrete\Core\Notification\View;


use Concrete\Core\Entity\Notification\UserSignupNotification;

class UserSignupListView extends StandardListView
{

    /**
     * @var UserSignupNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('User Signup');
    }

    public function getIconClass()
    {
        return 'fa fa-user';
    }

    public function getDetailedDescription()
    {
        $user = $this->notification->getUser();
        $ui = $user->getUserInfoObject();
        return t('New user %s registered for site.', $ui->getUserDisplayName());
    }

}
