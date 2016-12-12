<?php
namespace Concrete\Core\Express\Association\Builder;

use Concrete\Core\Database\Schema\BuilderInterface;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class OneToOneAssociationBuilder implements BuilderInterface
{
    protected $association;

    public function __construct(OneToOneAssociation $association)
    {
        $this->association = $association;
    }

    public function build(ClassMetadataBuilder $builder)
    {
        if ($this->association->getAssociationType() == OneToOneAssociation::TYPE_OWNING) {
            $builder->addOwningOneToOne(
                $this->association->getComputedTargetPropertyName(),
                $this->association->getTargetEntity()->getName(),
                $this->association->getComputedInversedByPropertyName()
            );
        } else {
            $builder->addInverseOneToOne(
                $this->association->getComputedTargetPropertyName(),
                $this->association->getTargetEntity()->getName(),
                $this->association->getComputedInversedByPropertyName()
            );
        }
    }
}
