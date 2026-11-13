<?php

namespace Concrete\Core\Permission\Event;

use Concrete\Core\Permission\ObjectInterface;
use Concrete\Core\User\User;
use Symfony\Contracts\EventDispatcher\Event;

class PermissionInheritanceEvent extends Event
{
    /**
     * @var ObjectInterface|mixed
     */
    protected $permissionObject;

    /**
     * @var string
     */
    protected $inheritanceMode;

    /**
     * @var User|null
     */
    protected $applier;

    public function __construct(
        $permissionObject,
        string $inheritanceMode,
        ?User $applier = null
    ) {
        $this->permissionObject = $permissionObject;
        $this->inheritanceMode = $inheritanceMode;
        $this->applier = $applier;
    }

    public function getPermissionObject()
    {
        return $this->permissionObject;
    }

    public function getInheritanceMode(): string
    {
        return $this->inheritanceMode;
    }

    public function getMode(): string
    {
        return $this->inheritanceMode;
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
