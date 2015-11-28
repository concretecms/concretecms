<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\AssociationBuilder\OneToManyAssociationBuilder;

/**
 * @Entity
 */
class OneToManyAssociation extends Association
{

    public function getAssociationBuilder()
    {
        return new OneToManyAssociationBuilder($this);
    }
}