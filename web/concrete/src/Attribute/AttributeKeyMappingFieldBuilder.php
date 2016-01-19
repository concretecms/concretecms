<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Database\Schema\BuilderInterface;
use Doctrine\ORM\Mapping\Builder\AssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class AttributeKeyMappingFieldBuilder implements BuilderInterface
{
    protected $key;
    public function __construct(Key $key)
    {
        $this->key = $key;
    }

    public function build(ClassMetadataBuilder $builder)
    {
        $class = $this->key->getAttributeKeyType()->getAttributeValue();
        $namingStrategy = \Core::make('Concrete\Core\Express\NamingStrategy');
        $associationBuilder = new AssociationBuilder($builder,
            array(
                'fieldName' => $this->key->getAttributeKeyHandle(),
                'targetEntity' => get_class($class),
            ),
            \Doctrine\ORM\Mapping\ClassMetadata::ONE_TO_ONE
        );
        $associationBuilder->cascadeAll();
        $associationBuilder->addJoinColumn(
            $namingStrategy->joinColumnName($this->key->getAttributeKeyHandle()),
            'avID'
        );

        return $associationBuilder->build();
    }
}
