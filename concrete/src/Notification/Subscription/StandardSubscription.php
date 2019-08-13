<?php
namespace Concrete\Core\Notification\Subscription;

/**
 * @since 8.0.0
 */
class StandardSubscription implements SubscriptionInterface
{

    protected $name;
    protected $identifier;

    public function __construct($identifier, $name)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    public function getSubscriptionIdentifier()
    {
        return $this->identifier;
    }

    public function getSubscriptionName()
    {
        return $this->name;
    }

}
