<?php
namespace Concrete\Core\Permission\Registry;

use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;

/**
 * @since 8.0.0
 */
class ObjectAssignment extends AbstractAssignment
{

    public function __construct(ObjectInterface $objectEntry, AccessRegistryInterface $registry)
    {
        $this->setEntry($objectEntry);
        $this->setRegistry($registry);
    }


}