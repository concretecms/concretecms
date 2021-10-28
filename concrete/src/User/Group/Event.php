<?php
namespace Concrete\Core\User\Group;

class Event
{
    protected $g;

    public function __construct(Group $g)
    {
        $this->g = $g;
    }

    public function getGroupObject()
    {
        return $this->g;
    }
}
