<?php
namespace Concrete\Core\Express\Association\Builder;

use Concrete\Core\Database\Schema\BuilderInterface;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class ManyToManyAssociationBuilder implements BuilderInterface
{
    protected $association;

    public function __construct(ManyToManyAssociation $association)
    {
        $this->association = $association;
    }

    public function build(ClassMetadataBuilder $builder)
    {
        if ($this->association->getAssociationType() == ManyToManyAssociation::TYPE_OWNING) {
            $builder->addOwningManyToMany(
                $this->association->getComputedTargetPropertyName(),
                $this->association->getTargetEntity()->getName(),
                $this->association->getComputedInversedByPropertyName()
            );
        } else {
            $builder->addInverseManyToMany(
                $this->association->getComputedTargetPropertyName(),
                $this->association->getTargetEntity()->getName(),
                $this->association->getComputedInversedByPropertyName()
            );
        }
    }
}
