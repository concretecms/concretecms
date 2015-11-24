<?php
namespace Concrete\Core\Database\Schema;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

interface FieldBuilderInterface
{

    public function buildField(ClassMetadataBuilder $builder);


}