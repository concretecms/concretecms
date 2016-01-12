<?php
namespace Concrete\Core\Attribute\Key;

use Doctrine\ORM\EntityManager;

class Factory
{

    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function getInstanceByID($akID)
    {
        return $this->getByID($akID);
    }

    public function getByID($akID)
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\Key')
            ->findOneBy(array('akID' => $akID));
    }
}
