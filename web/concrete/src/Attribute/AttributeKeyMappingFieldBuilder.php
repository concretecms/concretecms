<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Database\Schema\BuilderInterface;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class AttributeKeyMappingFieldBuilder implements BuilderInterface
{

    protected $key;
    public function __construct(AttributeKey $key)
    {
        $this->key = $key;
    }


    public function build(ClassMetadataBuilder $builder)
    {
        $definition = $this->key->getFieldMappingDefinition();
        $fields = array();
        if (isset($definition['type'])) {
            $fields[] = array(
                'name' => $this->key->getAttributeKeyHandle(),
                'type' => $definition['type'],
                'options' => $definition['options'],
            );
        } else {
            foreach ($definition as $name => $column) {
                $fields[] = array(
                    'name' => $this->key->getAttributeKeyHandle() . '_' . $name,
                    'type' => $column['type'],
                    'options' => $column['options'],
                );
            }
        }

        foreach($fields as $field) {
            $builder->addField($field['name'], $field['type'], $field['options']);
        }

    }


}