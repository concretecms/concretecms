<?php

namespace Concrete\Core\Permission\Event;

use Concrete\Core\User\User;
use Symfony\Contracts\EventDispatcher\Event;

class PermissionInheritanceEvent extends Event
{
    /**
     * @var object|null
     */
    protected $permissionObject;

    /**
     * @var string
     */
    protected $inheritanceMode;

    /**
     * @var User|null
     */
    protected $user;

    /**
     * @param object|null $permissionObject
     * @param string $inheritanceMode
     * @param User|null $user
     */
    public function __construct($permissionObject, $inheritanceMode, $user = null)
    {
        $this->permissionObject = $permissionObject;
        $this->inheritanceMode = $inheritanceMode;
        $this->user = $user;
    }

    /**
     * @return object|null
     */
    public function getPermissionObject()
    {
        return $this->permissionObject;
    }

    /**
     * @return string
     */
    public function getInheritanceMode()
    {
        return $this->inheritanceMode;
    }

    /**
     * @return User|null
     */
    public function getUserObject()
    {
        return $this->user;
    }
}
