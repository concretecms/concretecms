<?php
namespace Concrete\Core\User\Event;

class DeleteUser extends UserInfo
{
    protected $proceed = true;

    public function cancelDelete()
    {
        $this->proceed = false;
    }

    public function proceed()
    {
        return $this->proceed;
    }
}
