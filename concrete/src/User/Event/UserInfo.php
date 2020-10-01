<?php

namespace Concrete\Core\User\Event;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\UserInfo as ConcreteUserInfo;

class UserInfo
{
    protected $ui;

    /**
     * @var \Concrete\Core\User\User
     */
    protected $applier;

    public function __construct(ConcreteUserInfo $ui)
    {
        $this->ui = $ui;
        $u = Application::getFacadeApplication()->make(\Concrete\Core\User\User::class);
        if ($u->isRegistered()) {
            $this->applier = $u;
        }
    }

    /**
     * @return \Concrete\Core\User\User
     */
    public function getApplier()
    {
        return $this->applier;
    }

    /**
     * @param \Concrete\Core\User\User $applier
     */
    public function setApplier(\Concrete\Core\User\User $applier)
    {
        $this->applier = $applier;
    }

    public function getUserInfoObject()
    {
        return $this->ui;
    }
}
