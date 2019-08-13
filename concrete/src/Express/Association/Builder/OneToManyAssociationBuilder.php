<?php
namespace Concrete\Core\Express\Association\Builder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

/**
 * @since 8.0.0
 */
class OneToManyAssociationBuilder extends AbstractAssociationBuilder
{
    public function build(ClassMetadataBuilder $builder)
    {
        $builder->addOneToMany(
            $this->association->getComputedTargetPropertyName(),
            $this->association->getSourceEntity()->getName(),
            $this->association->getComputedInversedByPropertyName()
        );
    }
}
