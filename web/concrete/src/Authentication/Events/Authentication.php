<?php
namespace Concrete\Core\Authentication\Events;

use Concrete\Core\Authentication\AuthenticationType;
use User;

class Authentication extends \Symfony\Component\EventDispatcher\Event
{

    /** @var User */
    protected $user;

    /** @var AuthenticationType */
    protected $type;

    /**
     * @param AuthenticationType $type
     * @param User               $user
     */
    public function __construct(AuthenticationType $type, User $user)
    {
        $this->user = $user;
        $this->type = $type;
    }

    /**
     * @return AuthenticationType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

}
