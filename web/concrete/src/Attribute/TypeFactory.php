<?php

namespace Concrete\Core\Attribute;

use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Type as AttributeType;

/**
 * Factory class for creating and retrieving instances of the Attribute type entity.
 */
class TypeFactory
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getByHandle($atHandle)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Type');
        return $r->findOneBy(array('atHandle' => $atHandle));
    }

    public function add($atHandle, $atName, $pkg = null)
    {
        $type = new AttributeType();
        $type->setAttributeTypeName($atName);
        $type->setAttributeTypeHandle($atHandle);
        if ($pkg) {
            $type->setPackage($pkg);
        }
        $this->entityManager->persist($type);
        $this->entityManager->flush();
        return $type;
    }

}
