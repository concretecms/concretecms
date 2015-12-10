<?php

namespace Concrete\Core\Entity\Express;

use Concrete\Core\Express\Association\Formatter\ManyToOneFormatter;
use Concrete\Core\Express\Association\Builder\ManyToOneAssociationBuilder;
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

    public function getFormatter()
    {
        return new ManyToOneFormatter($this);
    }
}