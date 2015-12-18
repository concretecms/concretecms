<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Set as AttributeSet;
use Concrete\Core\Entity\Attribute\SetKey;
use Doctrine\ORM\EntityManager;


/**
 * Handles adding and removing keys from attribute sets
 *
 */
class SetManager
{

    protected $entityManager;
    protected $set;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setAttributeSet(AttributeSet $set)
    {
        $this->set = $set;
    }


    public function addKey(Key $key)
    {
        $displayOrder = 0;
        $keys = $this->set->getAttributeKeys();
        if (count($keys) > 0) {
            $displayOrder = count($keys);
        }

        $setKey = new SetKey();
        $setKey->setAttributeKey($key);
        $setKey->setAttributeSet($this->set);
        $setKey->setDisplayOrder($displayOrder);
        $this->set->getAttributeKeys()->add($setKey);
        $this->entityManager->persist($setKey);
        $this->entityManager->flush();
    }

}
