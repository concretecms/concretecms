<?php

namespace Concrete\Controller\Dialog\Permissions;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Conversation\Conversation as ConcreteConversation;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;

defined('C5_EXECUTE') or die('Access Denied.');

class Conversation extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/conversation';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $csp = new Checker($this->getConversation());

        return $csp->canEditConversationPermissions();
    }

    public function view()
    {
        $this->set('conversation', $this->getConversation());
    }

    protected function getConversation(): ?ConcreteConversation
    {
        $conversationID = $this->request->request->get('cnvID', $this->request->query->get('cnvID'));
        if (!$this->app->make(Numbers::class)->integer($conversationID, 1)) {
            return null;
        }
        $conversation = ConcreteConversation::getByID($conversationID);
        if ($conversation === null) {
            throw new UserMessageException(t('Conversation not found'));
        }

        return $conversation;
    }
}
