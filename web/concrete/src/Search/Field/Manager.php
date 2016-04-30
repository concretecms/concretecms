<?php
namespace Concrete\Core\Search\Field;

class Manager implements ManagerInterface
{

    protected $groups = [];

    public function getGroups()
    {
        return $this->groups;
    }

    public function addGroup($name, $fields = [])
    {
        $group = new Group();
        $group->setName($name);
        $group->setFields($fields);
        $this->addGroupObject($group);
    }
    public function addGroupObject(GroupInterface $group)
    {
        $this->groups[] = $group;
    }


}
