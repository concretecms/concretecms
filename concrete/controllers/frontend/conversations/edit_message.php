<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Area\Area;
use Concrete\Core\Block\Block;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Conversation\Editor\Editor;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Form\Service\Form as FormService;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class EditMessage extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/edit_message';

    /**
     * @var int|false|null FALSE if and only if not yet initialized
     */
    private $pageID = false;

    /**
     * @var string|null NULL if and only if not yet initialized
     */
    private $areaHandle;

    /**
     * @var int|false|null FALSE if and only if not yet initialized
     */
    private $blockID = false;

    public function view(): ?Response
    {
        $message = $this->getMessage();
        $this->checkMessage($message);
        $this->prepareViewSets($message);

        return null;
    }

    protected function getPageID(): ?int
    {
        if ($this->pageID === false) {
            $pageID = $this->request->request->get('cID');
            if ($this->app->make(Numbers::class)->integer($pageID, 1)) {
                $this->pageID = (int) $pageID;
            } else {
                $this->pageID = null;
            }
        }

        return $this->pageID;
    }

    protected function getAreaHandle(): string
    {
        if ($this->areaHandle === null) {
            $areaHandle = $this->request->request->get('blockAreaHandle');
            $this->areaHandle = is_string($areaHandle) ? $areaHandle : '';
        }

        return $this->areaHandle;
    }

    protected function getBlockID(): ?int
    {
        if ($this->blockID === false) {
            $blockID = $this->request->request->get('bID');
            if ($this->app->make(Numbers::class)->integer($blockID, 1)) {
                $this->blockID = (int) $blockID;
            } else {
                $this->blockID = null;
            }
        }

        return $this->blockID;
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
        $pageObj = $this->getPageID() === null ? null : Page::getByID($this->getPageID());
        if (!$pageObj || $pageObj->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $areaObj = $this->getAreaHandle() === '' ? null : Area::get($pageObj, $this->getAreaHandle());
        $blockObj = $areaObj && !$areaObj->isError() && $this->getBlockID() !== null ? Block::getByID($this->getBlockID(), $pageObj, $areaObj) : null;
        if (!$blockObj || $blockObj->isError()) {
            throw new UserMessageException(t('Unable to find the specified block'));
        }
        if ($blockObj->getBlockTypeHandle() !== BLOCK_HANDLE_CONVERSATION) {
            throw new UserMessageException(t('Invalid block'));
        }
        if ($blockObj->getController()->getConversationObject()->getConversationID() != $message->getConversationObject()->getConversationID()) {
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
        $this->set('attachmentsEnabled', $this->areArrachmentsEnabled($message));
        $this->set('userInfo', $this->getUserInfo());
    }

    protected function getEditor(Message $message): Editor
    {
        $editor = Editor::getByID($message->getConversationEditorID());
        $editor->setConversationMessageObject($message);

        return $editor;
    }

    protected function areArrachmentsEnabled(Message $message): bool
    {
        $conversation = $message->getConversationObject();
        if ($conversation->getConversationAttachmentOverridesEnabled() > 0) {
            return (bool) $conversation->getConversationAttachmentsEnabled();
        }
        $config = $this->app->make('config');

        return (bool) $config->get('conversations.attachments_enabled');
    }

    protected function getUserInfo(): object
    {
        $u = $this->app->make(User::class);

        return $u->isRegistered() ? $this->app->make(UserInfoRepository::class)->getByID($u->getUserID()) : null;
    }
}
