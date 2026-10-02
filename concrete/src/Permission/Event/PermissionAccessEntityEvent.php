<?php

namespace Concrete\Core\Permission\Event;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Duration;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\User\User;
use Symfony\Contracts\EventDispatcher\Event;

class PermissionAccessEntityEvent extends Event
{
    /**
     * @var Access
     */
    protected $permissionAccess;

    /**
     * @var Entity
     */
    protected $accessEntity;

    /**
     * @var Duration|null
     */
    protected $duration;

    /**
     * @var int
     */
    protected $accessType;

    /**
     * @var Key|null
     */
    protected $permissionKey;

    /**
     * @var ObjectInterface|null
     */
    protected $permissionObject;

    /**
     * @var User|null
     */
    protected $applier;

    public function __construct(
        Access $permissionAccess,
        Entity $accessEntity,
        ?Duration $duration = null,
        int $accessType = Key::ACCESS_TYPE_INCLUDE,
        ?User $applier = null,
        ?Key $permissionKey = null,
        ?ObjectInterface $permissionObject = null
    ) {
        $this->permissionAccess = $permissionAccess;
        $this->accessEntity = $accessEntity;
        $this->duration = $duration;
        $this->accessType = $accessType;
        $this->applier = $applier;
        $this->permissionKey = $permissionKey ?: $permissionAccess->getPermissionKeyObject();
        $this->permissionObject = $permissionObject ?: ($this->permissionKey ? $this->permissionKey->getPermissionObject() : null);
    }

    public function getPermissionAccess(): Access
    {
        return $this->permissionAccess;
    }

    public function getPermissionAccessObject(): Access
    {
        return $this->permissionAccess;
    }

    public function getAccessEntity(): Entity
    {
        return $this->accessEntity;
    }

    public function getAccessEntityObject(): Entity
    {
        return $this->accessEntity;
    }

    public function getDuration(): ?Duration
    {
        return $this->duration;
    }

    public function getDurationObject(): ?Duration
    {
        return $this->duration;
    }

    public function getAccessType(): int
    {
        return $this->accessType;
    }

    public function getPermissionKey(): ?Key
    {
        return $this->permissionKey;
    }

    public function getPermissionKeyObject(): ?Key
    {
        return $this->permissionKey;
    }

    public function getPermissionObject(): ?ObjectInterface
    {
        return $this->permissionObject;
    }

    public function getApplier(): ?User
    {
        return $this->applier;
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
