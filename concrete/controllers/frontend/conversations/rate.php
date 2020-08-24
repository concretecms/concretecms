<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Conversation\Rating\Type as RatingType;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Validation\Numbers;
use IPLib\Address\AddressInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class Rate extends FrontendController
{
    public function view(): Response
    {
        $message = $this->getMessage();
        $this->checkMessage($message);
        $message->rateMessage(
            $this->getRatingType(),
            (string) $this->app->make(AddressInterface::class),
            $this->getRatingUserID() ?: 0
        );

        return $this->app->make(ResponseFactoryInterface::class)->json(true);
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
        $msp = new Checker($message);
        if (!$msp->canRateConversationMessage()) {
            throw new UserMessageException(t('Access Denied.'));
        }
    }

    protected function getRatingTypeHandle(): string
    {
        $ratingTypeHandle = $this->request->request->get('cnvRatingTypeHandle');

        return is_string($ratingTypeHandle) ? $ratingTypeHandle : '';
    }

    protected function getRatingType(): RatingType
    {
        $ratingTypeHandle = $this->getRatingTypeHandle();
        $ratingType = $ratingTypeHandle === '' ? null : RatingType::getByHandle($ratingTypeHandle);

        if ($ratingType === null) {
            throw new UserMessageException(t('Invalid rating type handle.'));
        }

        return $ratingType;
    }

    protected function getRatingUserID(): ?int
    {
        $user = $this->app->make(User::class);

        return $user->isRegistered() ? (int) $user->getUserID() : null;
    }
}
