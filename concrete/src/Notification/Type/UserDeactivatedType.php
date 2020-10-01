<?php

namespace Concrete\Core\Notification\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Notification\Alert\Filter\FilterInterface;
use Concrete\Core\Notification\Alert\Filter\StandardFilter;
use Concrete\Core\Notification\Notifier\StandardNotifier;
use Concrete\Core\Notification\Subject\SubjectInterface;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Concrete\Core\Notification\Subscription\SubscriptionInterface;
use RuntimeException;

class UserDeactivatedType implements TypeInterface
{

    const IDENTIFIER = 'user_deactivated';

    /**
     * The application we use to build dependencies
     *
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * The notifier we use to notify
     *
     * @var \Concrete\Core\Notification\Notifier\StandardNotifier
     */
    protected $notifier;

    /**
     * The default subscription to output
     *
     * @var \Concrete\Core\Notification\Subscription\SubscriptionInterface
     */
    protected $defaultSubscription;

    /**
     * The filter we output by default
     *
     * @var \Concrete\Core\Notification\Alert\Filter\FilterInterface
     */
    protected $defaultFilter;

    public function __construct(
        Application $app,
        StandardNotifier $notifier,
        SubscriptionInterface $defaultSubscription = null,
        FilterInterface $defaultFilter = null
    ) {
        $this->app = $app;
        $this->notifier = $notifier;
        $this->defaultSubscription = $defaultSubscription;
        $this->defaultFilter = $defaultFilter;
    }

    /**
     * Get available type subscriptions
     *
     * @return \Concrete\Core\Notification\Subscription\SubscriptionInterface[]
     */
    public function getAvailableSubscriptions()
    {
        return [
            $this->getDefaultSubscription()
        ];
    }

    /**
     * Get the default subscription object.
     * If one was not passed in at construct time create one now
     *
     * @return \Concrete\Core\Notification\Subscription\SubscriptionInterface
     */
    protected function getDefaultSubscription()
    {
        if (!$this->defaultSubscription) {
            $this->defaultSubscription = new StandardSubscription(self::IDENTIFIER, t('User Deactivated'));
        }

        return $this->defaultSubscription;
    }

    /**
     * Get a subscription for a specific subject
     *
     * @param \Concrete\Core\Notification\Subject\SubjectInterface $subject
     *
     * @return \Concrete\Core\Notification\Subscription\SubscriptionInterface
     */
    public function getSubscription(SubjectInterface $subject)
    {
        return $this->getDefaultSubscription();
    }

    /**
     * Create a notification for a specific subject
     *
     * @param \Concrete\Core\Notification\Subject\SubjectInterface $subject
     *
     * @return \Concrete\Core\Express\Entry\Notifier\NotificationInterface
     */
    public function createNotification(SubjectInterface $subject)
    {
        throw new RuntimeException('Not supported.');
    }

    /**
     * Get available notification filters
     *
     * @return \Concrete\Core\Notification\Alert\Filter\FilterInterface[]
     */
    public function getAvailableFilters()
    {
        if (!$this->defaultFilter) {
            $this->defaultFilter = $this->app->make(
                StandardFilter::class,
                [
                    'type' => $this,
                    'key' => self::IDENTIFIER,
                    'name' => t('User Deactivated'),
                    'databaseNotificationType' => 'userdeactivatednotification'
                ]
            );
        }

        return [$this->defaultFilter];
    }

    /**
     * Get the notifier this type should use
     *
     * @return \Concrete\Core\Notification\Notifier\NotifierInterface
     */
    public function getNotifier()
    {
        return $this->notifier;
    }
}
