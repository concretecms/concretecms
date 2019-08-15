<?php
namespace Concrete\Controller\Dialog\Conversation;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Controller\Controller;

class Subscribe extends Controller
{
    protected $controllerActionPath = '/ccm/system/dialogs/conversation/subscribe';

    protected function loadConversation($cnvID)
    {
        $conversation = \Concrete\Core\Conversation\Conversation::getByID($cnvID);
        if (is_object($conversation) && $conversation->getConversationSubscriptionEnabled()) {
            $cp = new \Concrete\Core\Permission\Checker($conversation);
            if ($cp->canViewConversation()) {
                $u = new \Concrete\Core\User\User();
                $this->user = $u;
                $this->conversation = $conversation;
                $this->set('conversation', $conversation);
                $this->set('isSubscribed', $conversation->isUserSubscribed($u));
                $this->setViewObject(new \Concrete\Core\View\View('/dialogs/conversation/subscribe'));
            }
        }

        if (!$conversation) {
            throw new \Exception(t('Access Denied.'));
        }
    }

    public function view($cnvID)
    {
        $this->loadConversation($cnvID);
    }

    public function subscribe($cnvID)
    {
        $this->loadConversation($cnvID);
        $this->conversation->subscribeUser($this->user);
        $o = new EditResponse();
        $o->setAdditionalDataAttribute('subscribed', true);
        $o->outputJSON();
    }

    public function unsubscribe($cnvID)
    {
        $this->loadConversation($cnvID);
        $this->conversation->unsubscribeUser($this->user);
        $o = new EditResponse();
        $o->setAdditionalDataAttribute('subscribed', false);
        $o->outputJSON();
    }
}
