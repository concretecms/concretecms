<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class GetRating extends FrontendController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/get_rating';

    public function view(): ?Response
    {
        $this->set('message', $this->getMessage());

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
}
