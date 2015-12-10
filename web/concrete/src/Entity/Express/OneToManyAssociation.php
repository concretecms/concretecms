<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\Association\Builder\OneToManyAssociationBuilder;
use Concrete\Core\Express\Association\Formatter\OneToManyFormatter;

/**
 * @Entity
 */
class OneToManyAssociation extends Association
{

    public function getAssociationBuilder()
    {
        return new OneToManyAssociationBuilder($this);
    }

    public function getFormatter()
    {
        return new OneToManyFormatter($this);
    }

}