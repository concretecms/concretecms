<?php

namespace Concrete\Core\Permission\Event;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\User;
use Symfony\Contracts\EventDispatcher\Event;

class PermissionAssignmentEvent extends Event
{
    /**
     * @var Key|null
     */
    protected $permissionKey;

    /**
     * @var Access|null
     */
    protected $permissionAccess;

    /**
     * @var object|null
     */
    protected $permissionObject;

    /**
     * @var User|null
     */
    protected $user;

    /**
     * @param Key|null $permissionKey
     * @param Access|null $permissionAccess
     * @param object|null $permissionObject
     * @param User|null $user
     */
    public function __construct($permissionKey = null, $permissionAccess = null, $permissionObject = null, $user = null)
    {
        $this->permissionKey = $permissionKey;
        $this->permissionAccess = $permissionAccess;
        $this->permissionObject = $permissionObject;
        $this->user = $user;
    }

    /**
     * @return Key|null
     */
    public function getPermissionKey()
    {
        return $this->permissionKey;
    }

    /**
     * @return Access|null
     */
    public function getPermissionAccess()
    {
        return $this->permissionAccess;
    }

    /**
     * @return object|null
     */
    public function getPermissionObject()
    {
        return $this->permissionObject;
    }

    /**
     * @return User|null
     */
    public function getUserObject()
    {
        return $this->user;
    }
}
