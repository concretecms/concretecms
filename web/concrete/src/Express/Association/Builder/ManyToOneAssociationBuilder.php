<?php
namespace Concrete\Core\Express\Association\Builder;

use Concrete\Core\Database\Schema\BuilderInterface;
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
