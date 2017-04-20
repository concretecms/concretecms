<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Set as SetEntity;
use Concrete\Core\Entity\Attribute\SetKey;
use Doctrine\ORM\EntityManager;

/**
 * Handles adding and removing keys from attribute sets.
 */
class StandardSetManager implements SetManagerInterface
{
    const ASET_ALLOW_NONE = 0;
    const ASET_ALLOW_SINGLE = 1;
    const ASET_ALLOW_MULTIPLE = 2;

    protected $entityManager;
    protected $allowAttributeSets = false;
    protected $categoryEntity;

    /**
     * @return boolean
     */
    public function allowAttributeSets()
    {
        return $this->categoryEntity->allowAttributeSets();
    }

    public function getUnassignedAttributeKeys()
    {
        $attributes = array();
        foreach ($this->categoryEntity->getController()->getList() as $key) {
            $query = $this->entityManager->createQuery(
                'select sk from \Concrete\Core\Entity\Attribute\SetKey sk where sk.attribute_key = :key'
            );
            $query->setParameter('key', $key);
            $query->setMaxResults(1);
            $r = $query->getOneOrNullResult();
            if (!is_object($r)) {
                $attributes[] = $key;
            }
        }

        return $attributes;
    }

    public function getAttributeSets()
    {
        return $this->categoryEntity->getAttributeSets();
    }

    /**
     * @param boolean $allowAttributeSets
     */
    public function setAllowAttributeSets($allowAttributeSets)
    {
        $this->allowAttributeSets = $allowAttributeSets;
    }

    public function __construct(Category $categoryEntity, EntityManager $entityManager)
    {
        $this->categoryEntity = $categoryEntity;
        $this->entityManager = $entityManager;
    }

    public function addSet($handle, $name, $pkg = null, $locked = null)
    {
        $set = new SetEntity();
        $set->setAttributeKeyCategory($this->categoryEntity);
        $set->setAttributeSetHandle($handle);
        $set->setAttributeSetName($name);
        if ($pkg) {
            $set->setPackage($pkg);
        }
        if ($locked) {
            $set->setAttributeSetIsLocked($locked);
        }
        $this->entityManager->persist($set);
        $this->entityManager->flush();
        return $set;
    }

    public function addKey(SetEntity $set, Key $key)
    {
        $displayOrder = 0;
        $keys = $set->getAttributeKeys();
        if (count($keys) > 0) {
            $displayOrder = count($keys);
        }

        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\SetKey');
        $setKey = $r->findOneBy(array('attribute_key' => $key, 'set' => $set));
        if (!is_object($setKey)) {
            $setKey = new SetKey();
            $setKey->setAttributeKey($key);
            $setKey->setAttributeSet($set);
            $setKey->setDisplayOrder($displayOrder);
            $set->getAttributeKeyCollection()->add($setKey);
            $this->entityManager->persist($setKey);
            $this->entityManager->flush();
        }
    }

    public function updateAttributeSetDisplayOrder($asIDs)
    {
        $db = \Database::connection();
        for ($i = 0; $i < count($asIDs); ++$i) {
            $db->executeQuery(
                "UPDATE AttributeSets SET asDisplayOrder = {$i} WHERE akCategoryID = ? AND asID = ?",
                array($this->categoryEntity->getAttributeKeyCategoryID(), $asIDs[$i])
            );
        }
    }
}
