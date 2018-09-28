<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Attribute\SetManagerInterface;

class Manager implements ManagerInterface
{
    /**
     * The groups of fields.
     *
     * @var GroupInterface[]
     */
    protected $groups = [];

    /**
     * {@inheritdoc}
     *
     * @see ManagerInterface::getGroups()
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
     * {@inheritdoc}
     *
     * @see ManagerInterface::getGroupByName()
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
     * {@inheritdoc}
     *
     * @see ManagerInterface::getFieldByKey()
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
     * {@inheritdoc}
     *
     * @see ManagerInterface::getFieldsFromRequest()
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

    protected function populateAttributeGroups(SetManagerInterface $setManager)
    {
        $attributeSets = $setManager->getAttributeSets();
        $unassigned = $setManager->getUnassignedAttributeKeys();

        $attributes = [];
        foreach($attributeSets as $set) {
            foreach($set->getAttributeKeys() as $key) {
                $field = new AttributeKeyField($key);
                $attributes[] = $field;
            }
            $this->addGroup($set->getAttributeSetDisplayName(), $attributes);
        }

        $attributes = [];
        foreach($unassigned as $key) {
            $field = new AttributeKeyField($key);
            $attributes[] = $field;
        }
        $this->addGroup(t('Other Attributes'), $attributes);

    }
}
