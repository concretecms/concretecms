<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\AssociationBuilder\OneToOneAssociationBuilder;

/**
 * @Entity
 */
class OneToOneAssociation extends Association
{

    public function getAssociationBuilder()
    {
        return new OneToOneAssociationBuilder($this);
    }



}