<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class MessageDetail extends FrontendController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/message_detail';

    public function view(): ?Response
    {
        $this->set('message', $this->getMessage());
        $this->set('displayMode', $this->getDisplayMode());
        $this->set('enablePosting', $this->isPostingEnabled());
        $this->set('enableCommentRating', $this->isCommentRatingEnabled());
        $this->set('displaySocialLinks', $this->isDisplaySocialLinks());

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

    protected function getDisplayMode(): string
    {
        $modes = ['flat', 'threaded'];
        $mode = $this->request->request->get('displayMode');

        return in_array($mode, $modes, true) ? $mode : 'threaded';
    }

    protected function isPostingEnabled(): bool
    {
        return (bool) $this->request->request->get('enablePosting');
    }

    protected function isCommentRatingEnabled(): bool
    {
        return (bool) $this->request->request->get('enableCommentRating');
    }

    protected function isDisplaySocialLinks(): bool
    {
        return (bool) $this->request->request->get('displaySocialLinks');
    }
}
