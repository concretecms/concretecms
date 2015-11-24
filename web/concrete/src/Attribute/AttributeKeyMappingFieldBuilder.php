<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeKeyFactoryInterface;
use Concrete\Core\Database\Schema\FieldBuilderInterface;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use \Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class AttributeKeyMappingFieldBuilder implements FieldBuilderInterface
{

    protected $key;
    public function __construct(AttributeKey $key)
    {
        $this->key = $key;
    }


    public function buildField(ClassMetadataBuilder $builder)
    {
        $definition = $this->key->getFieldMappingDefinition();
        $fields = array();
        if (isset($definition['type'])) {
            $fields[] = array(
                'name' => $this->key->getHandle(),
                'type' => $definition['type'],
                'options' => $definition['options'],
            );
        } else {
            foreach ($definition as $name => $column) {
                $fields[] = array(
                    'name' => $this->key->getHandle() . '_' . $name,
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