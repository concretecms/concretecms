<?php
namespace Concrete\Core\Express\FieldBuilder;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeKeyFactoryInterface;
use Concrete\Core\Database\Schema\FieldBuilderInterface;
use \Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class PrimaryKeyFieldBuilder implements FieldBuilderInterface
{

    public function buildField(ClassMetadataBuilder $builder)
    {
        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();
    }

}