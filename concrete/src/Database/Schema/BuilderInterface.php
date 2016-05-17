<?php
namespace Concrete\Core\Database\Schema;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

interface BuilderInterface
{
    public function build(ClassMetadataBuilder $builder);
}
