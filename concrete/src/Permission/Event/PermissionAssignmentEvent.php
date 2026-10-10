<?php

namespace Concrete\Core\Permission\Event;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\ObjectInterface;
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
     * @var ObjectInterface|null
     */
    protected $permissionObject;

    /**
     * @var User|null
     */
    protected $applier;

    public function __construct(
        ?Key $permissionKey = null,
        ?Access $permissionAccess = null,
        ?ObjectInterface $permissionObject = null,
        ?User $applier = null
    ) {
        $this->permissionKey = $permissionKey;
        $this->permissionAccess = $permissionAccess;
        $this->permissionObject = $permissionObject;
        $this->applier = $applier;
    }

    public function getPermissionKey(): ?Key
    {
        return $this->permissionKey;
    }

    public function getPermissionKeyObject(): ?Key
    {
        return $this->permissionKey;
    }

    public function setPermissionKey(?Key $permissionKey): void
    {
        $this->permissionKey = $permissionKey;
    }

    public function getPermissionAccess(): ?Access
    {
        return $this->permissionAccess;
    }

    public function getPermissionAccessObject(): ?Access
    {
        return $this->permissionAccess;
    }

    public function setPermissionAccess(?Access $permissionAccess): void
    {
        $this->permissionAccess = $permissionAccess;
    }

    public function getPermissionObject(): ?ObjectInterface
    {
        if ($this->permissionObject !== null) {
            return $this->permissionObject;
        }
        if ($this->permissionKey !== null) {
            return $this->permissionKey->getPermissionObject();
        }

        return null;
    }

    public function setPermissionObject(?ObjectInterface $permissionObject): void
    {
        $this->permissionObject = $permissionObject;
    }

    public function getApplier(): ?User
    {
        return $this->applier;
    }

    public function setApplier(?User $applier): void
    {
        $this->applier = $applier;
    }

    public function getUser(): ?User
    {
        return $this->applier;
    }

    public function getUserObject(): ?User
    {
        return $this->applier;
    }
}
