<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Search\ItemList\ItemList;
use Symfony\Component\HttpFoundation\Request;

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

    public function getFieldByKey($key)
    {
        foreach($this->groups as $group) {
            foreach($group->getFields() as $field) {
                if ($field->getKey() == $key) {
                    return $field;
                }
            }
        }
    }

    public function getFieldsFromRequest(array $request)
    {
        $keys = $request['field'];
        $fields = array();
        if (is_array($keys)) {
            foreach($this->groups as $group) {
                foreach($group->getFields() as $field) {
                    if (in_array($field->getKey(), $keys)) {
                        $field->loadDataFromRequest($request);
                        $fields[] = $field;
                    }
                }
            }
        }
        return $fields;
    }

}
