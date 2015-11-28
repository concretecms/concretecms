<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\AssociationBuilder\ManyToManyAssociationBuilder;

/**
 * @Entity
 */
class ManyToManyAssociation extends Association
{

    public function getAssociationBuilder()
    {
        return new ManyToManyAssociationBuilder($this);
    }

}