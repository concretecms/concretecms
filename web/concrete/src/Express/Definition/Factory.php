<?php
namespace Concrete\Core\Express\Definition;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeKeyFactoryInterface;
use \Concrete\Core\Entity\Express\Entity;

class Factory
{
    protected $column_name;

    public function __construct($column_name)
    {
        $this->column_name = $column_name;
    }

    public function buildFromArray($definition)
    {
        $fields = array();
        if (isset($definition['type'])) {
            $fields[] = array(
                'name' => $this->column_name,
                'type' => $definition['type'],
                'options' => $definition['options'],
            );
        } else {
            foreach ($definition as $name => $column) {
                $fields[] = array(
                    'name' => $this->column_name . '_' . $name,
                    'type' => $column['type'],
                    'options' => $column['options'],
                );
            }
        }

        $return = [];
        foreach($fields as $field) {
            $f = new Field();
            $f->setName($field['name']);
            $f->setType($field['type']);
            $f->setOptions($field['options']);
            $return[] = $f;
        }
        return $return;
    }

}