<?php

namespace Concrete\Controller\Frontend\Conversations;

use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Validation\CSRF\Token;

defined('C5_EXECUTE') or die('Access Denied.');

class DeleteFile extends FrontendController
{
    public function view(): Response
    {
        $attachmentID = $this->getAttachmentID();
        $message = $attachmentID === null ? null : Message::getByAttachmentID($attachmentID);
        if ($message === null) {
            throw new UserMessageException(t('Invalid Attachment.'));
        }
        $mp = new Checker($message);
        if (!$mp->canEditConversationMessage()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        /** @var Token $token */
        $token = $this->app->make(Token::class);
        if ($token->validate("delete_conversation_message",$this->request->request->get('token'))) {
            throw new UserMessageException($token->getErrorMessage());
        }

        $message->removeFile($attachmentID);

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'attachmentID' => $attachmentID,
        ]);
    }

    protected function getAttachmentID(): ?int
    {
        $attachmentID = $this->request->request->get('cnvMessageAttachmentID');

        return $this->app->make(Numbers::class)->integer($attachmentID, 1) ? (int) $attachmentID : null;
    }
}
