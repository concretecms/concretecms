<?php
namespace Concrete\Core\Express\FieldBuilder;

use Concrete\Core\Database\Schema\BuilderInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class PrimaryKeyFieldBuilder implements BuilderInterface
{
    public function build(ClassMetadataBuilder $builder)
    {
        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();
    }
}
