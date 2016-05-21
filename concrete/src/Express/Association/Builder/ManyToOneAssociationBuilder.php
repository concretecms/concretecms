<?php
namespace Concrete\Core\Express\Association\Builder;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class ManyToOneAssociationBuilder extends AbstractAssociationBuilder
{
    public function build(ClassMetadataBuilder $builder)
    {
        $builder->addManyToOne(
            $this->association->getComputedTargetPropertyName(),
            $this->association->getTargetEntity()->getName(),
            $this->association->getComputedInversedByPropertyName()
        );
    }
}
