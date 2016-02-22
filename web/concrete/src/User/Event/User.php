<?php
namespace Concrete\Core\User\Event;

use Symfony\Component\EventDispatcher\Event as AbstractEvent;
use Concrete\Core\User\User as ConcreteUser;

class User extends AbstractEvent
{
    protected $u;

    public function __construct(ConcreteUser $u)
    {
        $this->u = $u;
    }

    public function getUserObject()
    {
        return $this->u;
    }
}
