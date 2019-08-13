<?php
namespace Concrete\Core\Database\Schema;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

/**
 * @since 8.0.0
 */
interface BuilderInterface
{
    public function build(ClassMetadataBuilder $builder);
}
