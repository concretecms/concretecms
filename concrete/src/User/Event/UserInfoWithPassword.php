<?php
namespace Concrete\Core\User\Event;

class UserInfoWithPassword extends UserInfo
{
    protected $uPassword;

    public function setUserPassword($uPassword)
    {
        $this->uPassword = $uPassword;
    }

    public function getUserPassword()
    {
        return $this->uPassword;
    }
}
