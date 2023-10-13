<?php

namespace Concrete\Core\Conversation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Conversation\Message\AuthorFormatter;
use Concrete\Core\Conversation\Message\MessageEvent;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Mail\Service;
use Concrete\Core\Notification\Type\NewConversationMessageType;

defined('C5_EXECUTE') or die('Access Denied.');

class HandleNewConversationMessageCommandHandler
{

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var NewConversationMessageType
     */
    protected $notificationType;

    public function __construct(
        EventDispatcher $eventDispatcher,
        Application $app,
        NewConversationMessageType $notificationType
    ) {
        $this->app = $app;
        $this->eventDispatcher = $eventDispatcher;
        $this->notificationType = $notificationType;
    }

    public function __invoke(HandleNewConversationMessageCommand $command)
    {
        $message = $command->getMessage();
        $event = new MessageEvent($message);
        $this->eventDispatcher->dispatch('on_new_conversation_message', $event);

        $cnv = $message->getConversationObject();
        $author = $message->getConversationMessageAuthorObject();
        $cnvMessageBody = $message->getConversationMessageBody();
        // Not sure why this would ever be null but someone added this at one time.
        if ($cnv instanceof \Concrete\Core\Conversation\Conversation) {
            $cnv->updateConversationSummary();

            // Notify any "waiting for me" admin users that the conversation message has been posted.
            // Note: This is a separate process from "conversation subscribers." Conversation subscribers
            // takes place through email and is meant to be used by unprivileged users in a community
            // setting. Waiting for Me notifications go into the Dashboard and include both unapproved
            // and approved users.
            $notifier = $this->notificationType->getNotifier();
            $subscription = $this->notificationType->getSubscription($message);
            $notified = $notifier->getUsersToNotify($subscription, $message);
            $notification = $this->notificationType->createNotification($message);
            $notifier->notify($notified, $notification);

            if ($message->isConversationMessageApproved()) {
                // Send the emails.
                $command = new SendEmailsToConversationMessageSubscribersCommand($message);
                $this->app->executeCommand($command);
            }
        }
    }
}