<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\Editor\Editor;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Form as FormService;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class EditMessage extends FrontendController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/edit_message';

    public function view(): ?Response
    {
        $message = $this->getMessage();
        $this->checkMessage($message);
        $this->prepareViewSets($message);

        return null;
    }

    protected function getMessageID(): ?int
    {
        $messageID = $this->request->request->get('cnvMessageID');

        return $this->app->make(Numbers::class)->integer($messageID, 1) ? (int) $messageID : null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getMessage(): Message
    {
        $messageID = $this->getMessageID();
        $message = $messageID === null ? null : Message::getByID($messageID);
        if ($message === null) {
            throw new UserMessageException(t('Invalid message object.'));
        }

        return $message;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkMessage(Message $message): void
    {
        $conversation = $this->getBlockConversation();
        if ($conversation->getConversationID() != $message->getConversationObject()->getConversationID()) {
            throw new UserMessageException(t('Invalid Conversation.'));
        }
        $mp = new Checker($message);
        if (!$mp->canEditConversationMessage()) {
            throw new UserMessageException(t('You do not have access to edit this message.'));
        }
    }

    protected function prepareViewsets(Message $message): void
    {
        $this->set('form', $this->app->make(FormService::class));
        $this->set('token', $this->app->make(Token::class));
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));
        $this->set('cID', $this->getPageID());
        $this->set('blockAreaHandle', $this->getAreaHandle());
        $this->set('bID', $this->getBlockID());
        $this->set('message', $message);
        $this->set('editor', $this->getEditor($message));
        $this->set('attachmentsEnabled', $this->areArrachmentsEnabled());
        $this->set('userInfo', $this->getUserInfo());
    }

    protected function getEditor(Message $message): Editor
    {
        $editor = Editor::getByID($message->getConversationEditorID());
        $editor->setConversationMessageObject($message);

        return $editor;
    }

    protected function areArrachmentsEnabled(): bool
    {
        $conversation = $this->getBlockConversation();
        if ($conversation->getConversationAttachmentOverridesEnabled() > 0) {
            return (bool) $conversation->getConversationAttachmentsEnabled();
        }
        $config = $this->app->make('config');

        return (bool) $config->get('conversations.attachments_enabled');
    }

    protected function getUserInfo(): ?UserInfo
    {
        $u = $this->app->make(User::class);

        return $u->isRegistered() ? $this->app->make(UserInfoRepository::class)->getByID($u->getUserID()) : null;
    }
}
