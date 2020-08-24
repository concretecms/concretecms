<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class DeleteMessage extends FrontendController
{
    public function view(): Response
    {
        $errors = $this->app->make(ErrorList::class);
        try {
            $this->checkToken();
            $message = $this->getMessage();
            $this->checkMessage($message);
            if (!$errors->has()) {
                $this->deleteMessage($message);

                return $this->buildSuccessResponse($message);
            }
        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        return $this->buildErrorsResponse($errors);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkToken(): void
    {
        $token = $this->app->make('token');
        if (!$token->validate('delete_conversation_message', $this->request->request->get('token'))) {
            throw new UserMessageException($token->getErrorMessage());
        }
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
        $mp = new Checker($message);
        if (!$mp->canDeleteConversationMessage()) {
            throw new UserMessageException(t('You do not have access to delete this message.'));
        }
    }

    protected function deleteMessage(Message $message): void
    {
        $message->delete();
    }

    protected function buildSuccessResponse(Message $message): Response
    {
        $response = new EditResponse();
        $response->setMessage(t('Message deleted successfully.'));

        return $this->app->make(ResponseFactoryInterface::class)->json($response);
    }

    protected function buildErrorsResponse(ErrorList $errors): Response
    {
        return $this->app->make(ResponseFactoryInterface::class)->json($errors);
    }
}
