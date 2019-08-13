<?php
namespace Concrete\Core\Express\Association\Builder;

use Concrete\Core\Database\Schema\BuilderInterface;
use Concrete\Core\Entity\Express\Association;

/**
 * @since 8.0.0
 */
abstract class AbstractAssociationBuilder implements BuilderInterface
{
    protected $association;

    public function __construct(Association $association)
    {
        $this->association = $association;
    }
}
