<?php
namespace Concrete\Core\Notification\Subscription;

interface SubscriptionInterface
{

    /**
     * The plain text human readable name of this subscription, like 'Core Updates'
     *
     * @return string
     */
    public function getSubscriptionName();

    /**
     * The lowercase `snake_case` hamdle of the subscription, like `core_updates`
     *
     * @return string
     */
    public function getSubscriptionIdentifier();

}
