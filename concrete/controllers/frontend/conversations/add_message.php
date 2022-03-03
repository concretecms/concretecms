<?php

namespace Concrete\Controller\Frontend\Conversations;

use ArrayAccess;
use Concrete\Block\CoreConversation\Controller;
use Concrete\Core\Antispam\Service as AntispamService;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\ConversationService;
use Concrete\Core\Conversation\FlagType\FlagType;
use Concrete\Core\Conversation\FrontendController;
use Concrete\Core\Conversation\Message\Author;
use Concrete\Core\Conversation\Message\Message as ConversationMessage;
use Concrete\Core\Conversation\Message\MessageEvent;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key as PermissionKey;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Validation\BannedWord\Service as BannedWordService;
use Concrete\Core\Validator\String\EmailValidator;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Core\Events\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class AddMessage extends FrontendController
{
    /**
     * @var \Concrete\Core\Conversation\Message\Message|false|null FALSE if and oonly if not yet determined
     */
    private $parentMessage = false;

    public function view(): Response
    {
        $errors = $this->app->make(ErrorList::class);
        try {
            $this->checkToken();
            $this->checkUser();
            $author = $this->getAuthor($errors);
            $body = $this->getMessageBody($errors);
            $attachments = $this->getAttachments($errors);
            $review = $this->getReview($errors);
            $this->checkCaptcha($errors);
            if (!$errors->has()) {
                $message = ConversationMessage::add(
                    $this->getConversation(),
                    $author,
                    null,
                    $body,
                    $this->getParentMessage()
                );
                if ($review !== null) {
                    $message->setReview($review);
                }
                foreach ($attachments as $attachment) {
                    $message->attachFile($attachment);
                }
                if ($this->isSpam($message)) {
                    $this->processSpamMessage($message);
                } else {
                    $this->processValidMessage($message);
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

    protected function getConversationID(): ?int
    {
        $conversationID = $this->request->request->get('cnvID');

        return $this->app->make(Numbers::class)->integer($conversationID, 1) ? (int) $conversationID : null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getConversation(): Conversation
    {
        $conversation = $this->getBlockConversation();
        if ((int) $conversation->getConversationID() !== $this->getConversationID()) {
            throw new UserMessageException(t('Invalid Conversation.'));
        }

        return $conversation;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getParentMessage(): ?ConversationMessage
    {
        if ($this->parentMessage === false) {
            $parentMessageID = (int) $this->request->request->get('cnvMessageParentID');
            if ($parentMessageID < 1) {
                $this->parentMessage = null;
            } else {
                $parentMessage = ConversationMessage::getByID($parentMessageID);
                if ($parentMessage === null) {
                    throw new UserMessageException(t('Invalid parent message.'));
                }
                if ($parentMessage->getConversationID() != $this->getConversation()->getConversationID()) {
                    throw new UserMessageException(t('Invalid parent message.'));
                }
                $this->parentMessage = $parentMessage;
            }
        }

        return $this->parentMessage;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkToken(): void
    {
        $val = $this->app->make('token');
        if (!$val->validate('add_conversation_message', $this->request->request->get('token'))) {
            throw new UserMessageException($val->getErrorMessage());
        }
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function checkUser(): void
    {
        $pk = PermissionKey::getByHandle('add_conversation_message');
        if (!$pk->validate()) {
            throw new UserMessageException(t('You do not have access to add a message to this conversation.'));
        }
    }

    protected function getAuthor(ArrayAccess $errors): Author
    {
        $author = new Author();
        $u = $this->app->make(User::class);
        if ($u->isRegistered()) {
            $author->setUser($u);
        } else {
            $vs = $this->app->make(Strings::class);
            $name = $this->request->request->get('cnvMessageAuthorName', '');
            if ($vs->notempty($name)) {
                $author->setName(trim($name));
            } else {
                $errors[] = t('You must enter your name to post this message.');
            }
            $email = $this->request->request->get('cnvMessageAuthorEmail', '');
            if ($this->app->make(EmailValidator::class)->isValid($email)) {
                $author->setEmail($email);
            } else {
                $errors[] = t('You must enter a valid email address to post this message.');
            }
            $website = $this->request->request->get('cnvMessageAuthorWebsite', '');

            if ($vs->notempty($website) !== false &&
                filter_var($website, FILTER_VALIDATE_URL) &&
                in_array(parse_url($website, PHP_URL_SCHEME), ["http", "https"])) {
                $author->setWebsite(trim($website));
            }
        }

        return $author;
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
        } else {
            $config = $this->app->make('config');
            if ($config->get('conversations.banned_words')) {
                if ($this->app->make(BannedWordService::class)->hasBannedWords($body)) {
                    $errors[] = t('Banned words detected.');
                }
            }
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
     * @throws UserMessageException
     */
    protected function getAttachments(ArrayAccess $errors): array
    {
        $attachmentIDs = $this->getAttachmentIDs();
        if ($attachmentIDs === []) {
            return [];
        }
        $attachments = [];
        $pp = new Checker($this->getConversation());
        if (!$pp->canAddConversationMessageAttachments()) {
            $errors[] = t('You do not have permission to add attachments.');
        } else {
            $blockController = $this->getBlockController();
            $u = $this->app->make(User::class);
            $maxFiles = $u->isRegistered() ? $blockController->getFileSettings()['maxFilesRegistered'] : $blockController->getFileSettings()['maxFilesGuest'];
            if ($maxFiles > 0 && count($attachmentIDs) > $maxFiles) {
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

    protected function getReview(ArrayAccess $errors): ?int
    {
        $review = $this->request->request->get('review');
        $review = empty($review) ? 0 : (int) $review;
        if ($review === 0) {
            $review = null;
        } else {
            $blockController = $this->getBlockController();
            $parentMessage = $this->getParentMessage();
            $canReview = $blockController->enableTopCommentReviews && $parentMessage === null;
            if ($canReview !== true) {
                $errors[] = t('Reviews have not been enabled for this discussion.');
                $review = null;
            } elseif ($review < 1 || $review > 5) {
                $errors[] = t('A review must be a rating between 1 and 5.');
                $review = null;
            }
        }

        return $review;
    }

    protected function checkCaptcha(ArrayAccess $errors): void
    {
        $u = $this->app->make(User::class);
        if (!$u->isRegistered()) {
            $captcha = $this->app->make('captcha');
            if (!$captcha->check()) {
                $errors[] = t('Incorrect image validation code. Please check the image and re-enter the letters or numbers as necessary.');
            }
        }
    }

    protected function isSpam(ConversationMessage $message): bool
    {
        return !$this->app->make(AntispamService::class)->check($message->getConversationMessageBody(), 'conversation_comment');
    }

    protected function processSpamMessage(ConversationMessage $message): void
    {
        $message->flag(FlagType::getByHandle('spam'));
    }

    protected function processValidMessage(ConversationMessage $message): void
    {
        $pk = PermissionKey::getByHandle('add_conversation_message');
        $assignment = $pk->getMyAssignment();
        if ($assignment->approveNewConversationMessages()) {
            $message->approve();
        }
    }

    protected function dispatchEvent(ConversationMessage $message): void
    {
        $event = new MessageEvent($message);
        $dispatcher = $this->app->make(EventDispatcher::class);
        $dispatcher->dispatch('on_conversations_message_add', $event);
    }

    protected function trackMessage(ConversationMessage $message): void
    {
        $conversationService = $this->app->make(ConversationService::class);
        $conversationService->trackReview($message, $this->getBlock());
    }

    protected function buildErrorsResponse(ErrorList $errors): Response
    {
        return $this->app->make(ResponseFactoryInterface::class)->json($errors);
    }

    protected function buildSuccessResponse(ConversationMessage $message): Response
    {
        return $this->app->make(ResponseFactoryInterface::class)->json($message);
    }
}
