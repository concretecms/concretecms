<?php
namespace Concrete\Core\User\Group;

use Concrete\Core\User\User;

abstract class GroupAutomationController
{
    /**
     * @deprecated What's deprecated is the "public" part: use the getGroupObject() method instead.
     *
     * @var \Concrete\Core\User\Group\Group
     */
    public $group;

    /** 
     * Return true to automatically enter the current ux into the group.
     */
    abstract public function check(User $ux);

    /**
     * @return \Concrete\Core\User\Group\Group
     */
    public function getGroupObject()
    {
        return $this->group;
    }

    public function __construct(Group $g)
    {
        $this->group = $g;
    }
}
