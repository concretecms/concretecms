<?php
namespace Concrete\Core\Express\Association\Builder;

use Concrete\Core\Database\Schema\BuilderInterface;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

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
