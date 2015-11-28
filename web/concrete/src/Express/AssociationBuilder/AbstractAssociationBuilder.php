<?php
namespace Concrete\Core\Express\AssociationBuilder;

use Concrete\Core\Database\Schema\BuilderInterface;
use Concrete\Core\Entity\Express\Association;

abstract class AbstractAssociationBuilder implements BuilderInterface
{

    protected $association;

    public function __construct(Association $association)
    {
        $this->association = $association;
    }


}
