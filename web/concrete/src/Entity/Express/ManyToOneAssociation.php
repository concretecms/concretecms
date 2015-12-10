<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\AssociationBuilder\ManyToOneAssociationBuilder;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

/**
 * @Entity
 */
class ManyToOneAssociation extends Association
{

    public function getAssociationBuilder()
    {
        return new ManyToOneAssociationBuilder($this);
    }

}