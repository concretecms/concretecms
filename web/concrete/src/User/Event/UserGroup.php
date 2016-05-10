<?php
namespace Concrete\Core\User\Event;

use Concrete\Core\User\Group\Group;

class UserGroup extends User
{
    protected $g;

    public function getGroupObject()
    {
        return $this->g;
    }

    public function setGroupObject(Group $g)
    {
        $this->g = $g;
    }
}
