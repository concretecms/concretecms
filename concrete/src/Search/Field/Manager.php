<?php
namespace Concrete\Core\Search\Field;

class Manager implements ManagerInterface
{
    /**
     * The groups of fields.
     *
     * @var Group[]|GroupInterface[]
     */
    protected $groups = [];

    /**
     * Get the groups of fields.
     *
     * @return Group[]|GroupInterface[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add a group of fields.
     *
     * @param string $name the group name
     * @param FieldInterface[] $fields
     */
    public function addGroup($name, $fields = [])
    {
        $group = new Group();
        $group->setName($name);
        $group->setFields($fields);
        $this->addGroupObject($group);
    }

    /**
     * Get a fields group by name.
     *
     * @param string $name
     *
     * @return Group|GroupInterface|null
     */
    public function getGroupByName($name)
    {
        foreach ($this->groups as $group) {
            if ($group->getName() == $name) {
                return $group;
            }
        }
    }

    /**
     * Add a field group.
     *
     * @param GroupInterface $group
     */
    public function addGroupObject(GroupInterface $group)
    {
        $this->groups[] = $group;
    }

    /**
     * Search a field across all the groups.
     *
     * @param string $key The field key
     *
     * @return FieldInterface|null
     */
    public function getFieldByKey($key)
    {
        foreach ($this->groups as $group) {
            foreach ($group->getFields() as $field) {
                if ($field->getKey() == $key) {
                    return $field;
                }
            }
        }
    }

    /**
     * Get the list of fields whose keys are in the 'field' of an array, and initialize the fields with the data.
     *
     * @param array $request
     *
     * @return FieldInterface[]
     */
    public function getFieldsFromRequest(array $request)
    {
        $keys = isset($request['field']) ? $request['field'] : null;
        $fields = [];
        if (is_array($keys)) {
            foreach ($this->groups as $group) {
                foreach ($group->getFields() as $field) {
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
