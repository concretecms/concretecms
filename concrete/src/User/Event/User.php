<?php
namespace Concrete\Core\User\Event;

use Concrete\Core\User\User as ConcreteUser;

class User
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
