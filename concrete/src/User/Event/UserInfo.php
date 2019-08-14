<?php
namespace Concrete\Core\User\Event;

use Symfony\Component\EventDispatcher\Event as AbstractEvent;
use Concrete\Core\User\UserInfo as ConcreteUserInfo;

class UserInfo extends AbstractEvent
{
    protected $ui;

    /**
     * @var \Concrete\Core\User\User
     * @since 8.5.0
     */
    protected $applier;

    /**
     * @return \Concrete\Core\User\User
     * @since 8.5.0
     */
    public function getApplier()
    {
        return $this->applier;
    }

    /**
     * @param \Concrete\Core\User\User $applier
     * @since 8.5.0
     */
    public function setApplier(\Concrete\Core\User\User $applier)
    {
        $this->applier = $applier;
    }

    public function __construct(ConcreteUserInfo $ui)
    {
        $this->ui = $ui;
        $u = new \Concrete\Core\User\User();
        if ($u->isRegistered()) {
            $this->applier = $u;
        }
    }

    public function getUserInfoObject()
    {
        return $this->ui;
    }
}
