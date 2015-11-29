<?php
namespace Concrete\Core\Express\AssociationBuilder;

use Concrete\Core\Database\Schema\BuilderInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class ManyToManyAssociationBuilder extends AbstractAssociationBuilder
{

    public function build(ClassMetadataBuilder $builder)
    {

        $builder->addOwningManyToMany(
            $this->association->getComputedTargetPropertyName(),
            $this->association->getSourceEntity()->getName()
        );
        $builder->addInverseManyToMany(
            $this->association->getComputedTargetPropertyName(),
            $this->association->getSourceEntity()->getName(),
            $this->association->getComputedInversedByPropertyName()
        );


    }

}
