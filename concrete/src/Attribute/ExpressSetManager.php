<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;


/**
 * Handles adding and removing keys from attribute sets.
 */
class ExpressSetManager implements SetManagerInterface
{

    protected $entityManager;
    protected $entity;

    public function allowAttributeSets()
    {
        return false;
    }

    public function getUnassignedAttributeKeys()
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\ExpressKey');
        return $r->findBy(array('entity' => $this->entity));
    }

    public function __construct(Entity $entity, EntityManager $entityManager)
    {
        $this->entity = $entity;
        $this->entityManager = $entityManager;
    }

    public function getAttributeSets()
    {
        return array();
    }

    public function updateAttributeSetDisplayOrder($sets)
    {
        return false;
    }

}
