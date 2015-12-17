<?php
namespace Concrete\Core\Express\FieldBuilder;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Schema\BuilderInterface;
use Concrete\Core\Database\Schema\FieldBuilderInterface;
use \Concrete\Core\Entity\Express\Entity;
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