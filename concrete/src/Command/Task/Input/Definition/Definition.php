<?php
namespace Concrete\Core\Command\Task\Input\Definition;

defined('C5_EXECUTE') or die("Access Denied.");

class Definition implements \JsonSerializable
{

    /**
     * @var FieldInterface[]
     */
    protected $fields = [];

    public function addField(FieldInterface $field)
    {
        $this->fields[] = $field;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function jsonSerialize()
    {
        return [
            'fields' => $this->fields,
        ];
    }
}
