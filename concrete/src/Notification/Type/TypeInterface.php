<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Notification\Alert\Filter\FilterFactoryInterface;
use Concrete\Core\Notification\Alert\Filter\FilterInterface;
use Concrete\Core\Notification\Alert\Filter\FilterList;
use Concrete\Core\Notification\Notifier\NotifierInterface;
use Concrete\Core\Notification\Subject\SubjectInterface;

interface TypeInterface
{

    /**
     * Get available type subscriptions
     *
     * @return \Concrete\Core\Notification\Subscription\SubscriptionInterface[]
     */
    public function getAvailableSubscriptions();

    /**
     * Get a subscription for a specific subject
     *
     * @param \Concrete\Core\Notification\Subject\SubjectInterface $subject
     *
     * @return \Concrete\Core\Notification\Subscription\SubscriptionInterface
     */
    public function getSubscription(SubjectInterface $subject);

    /**
     * Create a notification for a specific subject
     *
     * @param \Concrete\Core\Notification\Subject\SubjectInterface $subject
     *
     * @return \Concrete\Core\Express\Entry\Notifier\NotificationInterface
     */
    public function createNotification(SubjectInterface $subject);

    /**
     * Get the notifier this type should use
     *
     * @return \Concrete\Core\Notification\Notifier\NotifierInterface
     */
    public function getNotifier();

    /**
     * Get available notification filters
     *
     * @return \Concrete\Core\Notification\Alert\Filter\FilterInterface[]
     */
    public function getAvailableFilters();
}
