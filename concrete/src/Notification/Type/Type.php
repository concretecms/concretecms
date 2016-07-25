<?php
namespace Concrete\Core\Notification\Type;


use Concrete\Core\Notification\Notifier\StandardNotifier;
use Concrete\Core\Notification\Subscription\StandardSubscription;
use Doctrine\ORM\EntityManagerInterface;

abstract class Type implements TypeInterface
{

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getNotifier()
    {
        return new StandardNotifier($this->entityManager);
    }
}