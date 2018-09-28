<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManager;
use Gettext\Translations;

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

    public function getListByPackage(Package $package)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Set');

        return $r->findByPackage($package);
    }


    public function getByID($asID)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Set');

        return $r->findOneBy(array('asID' => $asID));
    }

    /**
     * @param $key Key
     */
    public function getByAttributeKey($key)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\SetKey');
        $query = $r->createQueryBuilder('sk')
            ->where('sk.attribute_key = :attribute_key');
        $query->setParameter('attribute_key', $key);
        $results = $query->getQuery()->getResult();
        $sets = array();
        foreach ($results as $result) {
            $sets[] = $result->getAttributeSet();
        }

        return array_unique($sets);
    }
    
    /**
     * @deprecated
     */
    public function exportTranslations()
    {
        $translations = new Translations();
        $sets = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Set')
            ->findAll();
        foreach($sets as $set) {
            $translations->insert('AttributeSet', $set->getAttributeSetName());
        }
        return $translations;
    }

}
