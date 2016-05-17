<?php
namespace Concrete\Core\Search\Field;

class Group implements GroupInterface
{

    protected $name;
    protected $fields = [];

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function addField(FieldInterface $field)
    {
        $this->fields[] = $field;
    }



}
