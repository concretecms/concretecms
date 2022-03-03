<?php

namespace Concrete\Controller\Frontend\Conversations;

use ArrayAccess;
use Concrete\Core\Conversation\ConversationService;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Message;
use Concrete\Core\Conversation\Message\MessageEvent;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class UpdateMessage extends FrontendController
{
    public function view(): Response
    {
        $errors = $this->app->make(ErrorList::class);
        try {
            $this->checkToken();
            $message = $this->getMessage();
            $body = $this->getMessageBody($errors);
            $attachments = $this->getAttachments($message, $errors);
            $review = $this->getReview($errors);
            if (!$errors->has()) {
                $message->setMessageBody($body);
                if ($review !== null) {
                    $message->setReview($review);
                }
                foreach ($attachments as $attachment) {
                    $message->attachFile($attachment);
                }
                $this->dispatchEvent($message);
                $this->trackMessage($message);

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
        $val = $this->app->make('token');
        if (!$val->validate('edit_conversation_message', $this->request->request->get('token'))) {
            throw new UserMessageException($val->getErrorMessage());
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
        if ($message === null || $this->getBlockConversation()->getConversationID() != $message->getConversationID()) {
            throw new UserMessageException(t('Invalid message object.'));
        }
        $mp = new Checker($message);
        if (!$mp->canEditConversationMessage()) {
            throw new UserMessageException(t('You do not have access to edit this message.'));
        }

        return $message;
    }

    protected function getRawMessageBody(): string
    {
        $body = $this->request->request->get('cnvMessageBody');

        return is_string($body) ? $body : '';
    }

    protected function getMessageBody(ArrayAccess $errors): string
    {
        $body = trim($this->getRawMessageBody());
        if ($body === '') {
            $errors[] = t('Your message cannot be empty.');
        }

        return $body;
    }

    /**
     * @return int[]
     */
    protected function getAttachmentIDs(): array
    {
        $attachmentIDs = $this->request->request->get('attachments');
        if (!is_array($attachmentIDs)) {
            return [];
        }

        return array_values( // Reset array indexes
            array_unique( // Remove duplicates
                array_filter( // Remove zeroes
                    array_map( // Ensure integer types
                        'intval',
                        $attachmentIDs
                    )
                )
            )
        );
    }

    /**
     * @return \Concrete\Core\Entity\File\File[]
     */
    protected function getAttachments(Message $message, ArrayAccess $errors): array
    {
        $attachmentIDs = $this->getAttachmentIDs();
        if ($attachmentIDs === []) {
            return [];
        }
        $attachments = [];
        $pp = new Checker($message->getConversationObject());
        if (!$pp->canAddConversationMessageAttachments()) {
            $errors[] = t('You do not have permission to add attachments.');
        } else {
            $blockController = $this->getBlockController();
            $u = $this->app->make(User::class);
            $maxFiles = $u->isRegistered() ? $blockController->maxFilesRegistered : $blockController->maxFilesGuest;
            $messageAttachmentCount = count($message->getAttachments($message->getConversationMessageID()));
            $totalCurrentAttachments = $messageAttachmentCount + count($attachmentIDs);
            if ($maxFiles > 0 && $totalCurrentAttachments > $maxFiles) {
                $errors[] = t('You have too many attachments.');
            } else {
                $em = $this->app->make(EntityManagerInterface::class);
                foreach ($attachmentIDs as $attachmentID) {
                    $file = $em->find(File::class, $attachmentID);
                    if ($file === null) {
                        $errors[] = t('Invalid file specified.');
                    } else {
                        $attachments[] = $file;
                    }
                }
            }
        }

        return $attachments;
    }

    protected function canReview(Message $message): bool
    {
        return $this->getBlockController()->enableTopCommentReviews && !$message->getConversationMessageParentID();
    }

    protected function getReview(ArrayAccess $errors): ?int
    {
        $review = $this->request->request->get('review');
        $review = empty($review) ? 0 : (int) $review;
        if ($review === 0) {
            $review = null;
        } else {
            if (!$this->canReview) {
                $errors[] = t('Reviews have not been enabled for this discussion.');
                $review = null;
            } elseif ($review < 1 || $review > 5) {
                $errors[] = t('A review must be a rating between 1 and 5.');
                $review = null;
            }
        }

        return $review;
    }

    protected function dispatchEvent(Message $message): void
    {
        $event = new MessageEvent($message);
        $dispatcher = $this->app->make('director');
        $dispatcher->dispatch('on_conversations_message_update', $event);
    }

    protected function trackMessage(Message $message): void
    {
        $conversationService = $this->app->make(ConversationService::class);
        $conversationService->trackReview($message, $this->getBlock());
    }

    protected function buildErrorsResponse(ErrorList $errors): Response
    {
        return $this->app->make(ResponseFactoryInterface::class)->json($errors);
    }

    protected function buildSuccessResponse(Message $message): Response
    {
        return $this->app->make(ResponseFactoryInterface::class)->json($message);
    }
}
