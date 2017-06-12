<?php
namespace Concrete\Core\Notification\View;


use Concrete\Core\Entity\Notification\UserSignupNotification;
use HtmlObject\Element;

class NewPrivateMessageListView extends StandardListView
{

    /**
     * @var UserSignupNotification
     */
    protected $notification;

    public function getTitle()
    {
        return t('New Private Message');
    }

    public function getIconClass()
    {
        return 'fa fa-envelope';
    }

    public function getInitiatorUserObject()
    {
        $message = $this->notification->getMessageObject();
        if (is_object($message)) {
            return $message->getMessageAuthorObject();
        }
    }

    public function getActionDescription()
    {
        $message = $this->notification->getMessageObject();
        return t('New private message: <a href="%s"><strong>%s</strong></a>.',
            \URL::to('/account/messages', 'view_message', 'inbox', $message->getMessageID()), $message->getFormattedMessageSubject());
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Sent By '));
    }



}
