<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;

class AccessAssignment extends AbstractAssignment
{

    public function __construct(EntityInterface $accessEntry, ObjectRegistryInterface $registry)
    {
        $this->setEntry($accessEntry);
        $this->setRegistry($registry);
    }


}