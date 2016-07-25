<?php
namespace Concrete\Core\Notification\View;


use Concrete\Core\Entity\Notification\UserSignupNotification;
use HtmlObject\Element;

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

    public function getInitiatorUserObject()
    {
        $createdBy = $this->notification->getSignupRequest()->getCreatedBy();
        if (is_object($createdBy)) {
            return $createdBy->getUserInfoObject();
        }
    }

    public function getActionDescription()
    {
        $user = $this->notification->getSignupRequest()->getUser();
        $ui = $user->getUserInfoObject();
        $createdBy = $this->notification->getSignupRequest()->getCreatedBy();
        if ($createdBy) {
            return t('New user %s created.', $ui->getUserDisplayName());
        } else {
            return t('New user %s registered for site.', $ui->getUserDisplayName());
        }
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Created By '));
    }



}
