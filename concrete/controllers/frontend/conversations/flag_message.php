<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Antispam\Service as AntispamService;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Conversation\FlagType\FlagType;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class FlagMessage extends FrontendController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/frontend/conversations/flag_message';

    /**
     * @param string $asJSON '0' (false) or '1' (true)
     */
    public function view(string $asJSON): ?Response
    {
        $asJSON = (bool) $asJSON;
        $this->checkToken();
        $message = $this->getMessage();
        $this->checkMessage($message);
        $this->flagMessage($message);

        return $this->buildSuccessResponse($message, $asJSON);
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkToken(): void
    {
        $token = $this->app->make('token');
        if (!$token->validate('flag_conversation_message', $this->request->request->get('token'))) {
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
        if (!$mp->canFlagConversationMessage()) {
            throw new UserMessageException(t('You do not have access to flag this message.'));
        }
    }

    protected function flagMessage(Message $message): void
    {
        $flagtype = FlagType::getByHandle('spam');
        $message->flag($flagtype);
        $message->unapprove();
        $author = $message->getConversationMessageAuthorObject();
        $as = $this->app->make(AntispamService::class);
        $as->report(
            $message->getConversationMessageBody(),
            $author->getName(),
            $author->getEmail(),
            $message->getConversationMessageSubmitIP(),
            $message->getConversationMessageSubmitUserAgent()
        );
    }

    protected function buildSuccessResponse(Message $message, bool $asJSON): ?Response
    {
        if ($asJSON) {
            $r = new EditResponse();
            $r->setMessage(t('Message flagged successfully.'));

            return $this->app->make(ResponseFactoryInterface::class)->json($r);
        }

        $this->set('message', $message);

        return null;
    }

    protected function buildErrorsResponse(UserMessageException $x): Response
    {
        $errors = $this->app->make(ErrorList::class);
        $errors->add($x);

        return $this->app->make(ResponseFactoryInterface::class)->json($errors);
    }
}
