<?php
namespace Concrete\Core\Search\Field;

class Group implements GroupInterface
{
    /**
     * The group name.
     *
     * @var string
     */
    protected $name;

    /**
     * The fields in this group.
     *
     * @var FieldInterface[]
     */
    protected $fields = [];

    /**
     * {@inheritdoc}
     *
     * @see GroupInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the group name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     *
     * @see GroupInterface::getFields()
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set the fields belonging to this group.
     *
     * @param FieldInterface[] $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Add a field to this group.
     *
     * @param FieldInterface $field
     */
    public function addField(FieldInterface $field)
    {
        $this->fields[] = $field;
    }
}
