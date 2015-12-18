<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Package\Package;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Set as AttributeSet;

/**
 * Factory class for creating and retrieving instances of the Attribute type entity.
 */
class SetFactory
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getByHandle($atHandle)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Set');
        return $r->findOneBy(array('asHandle' => $atHandle));
    }

    public function getByID($asID)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Set');
        return $r->findOneBy(array('asID' => $asID));
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
