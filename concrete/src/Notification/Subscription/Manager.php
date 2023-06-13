<?php
namespace Concrete\Core\Notification\Subscription;

defined('C5_EXECUTE') or die('Access Denied.');

class Manager
{
    /**
     * @var \Concrete\Core\Notification\Type\Manager
     */
    protected $typeManager;

    public function __construct(\Concrete\Core\Notification\Type\Manager $manager)
    {
        $this->typeManager = $manager;
    }

    public function getSubscriptions()
    {
        $subscriptions = array();
        foreach($this->typeManager->getDrivers() as $driver) {
            $subscriptions = array_merge($subscriptions, $driver->getAvailableSubscriptions());
        }
        return $subscriptions;
    }
}
