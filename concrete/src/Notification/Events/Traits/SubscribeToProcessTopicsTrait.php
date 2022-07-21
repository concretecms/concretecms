<?php

namespace Concrete\Core\Notification\Events\Traits;

use Concrete\Core\Notification\Events\MercureService;
use Concrete\Core\Notification\Events\ServerEvent\AbstractEvent;
use Concrete\Core\Notification\Events\ServerEvent\BatchUpdatedEvent;
use Concrete\Core\Notification\Events\ServerEvent\ProcessClosedEvent;
use Concrete\Core\Notification\Events\ServerEvent\ProcessOutputEvent;
use Concrete\Core\Notification\Events\ServerEvent\SubscribableEventInterface;
use Concrete\Core\Notification\Events\Subscriber;

trait SubscribeToProcessTopicsTrait
{

    public function subscribeToProcessTopicsIfNotificationEnabled($refreshCookie = true): ?Subscriber
    {
        $mercureService = app(MercureService::class);
        if ($mercureService->isEnabled()) {
            $events = [
                BatchUpdatedEvent::class,
                ProcessClosedEvent::class,
                ProcessOutputEvent::class,
            ];
            $subscriber = $mercureService->getSubscriber();
            foreach ($events as $event) {
                /**
                 * @var $event SubscribableEventInterface
                 */
                $subscriber->addTopic($event::getTopicForSubscribing());
            }
            if ($refreshCookie) {
                $subscriber->refreshAuthorizationCookie();
            }
            return $subscriber;
        }
        return null;
    }
}

