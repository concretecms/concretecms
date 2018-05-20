<?php
namespace Concrete\Core\Attribute;

/**
 * Handles adding and removing keys from User attribute sets.
 */
class UserKeySetManager extends StandardSetManager
{

    /**
     * Method that return all attributes common to all user and also associated to groups received as parameter
     * @param array $groups
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUnassignedAttributeKeysCommonAndAssociatedToGroups($groups=array())
    {
        $attributes = array();
        foreach ($this->categoryEntity->getController()->getUserKeyList($groups) as $key) {
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

    /**
     * Method that return only all attributes commons and associated to user groups available in set received
     * Note: the set return  by default all User Keys available and for this reason we must get only attributes common for all users and attributes associated to user groups and ignore others
     * @param array $groups
     * @param \Concrete\Core\Entity\Attribute\Set $set
     * @return array
     */
    public function getSetAttributesKeys($groups=array(), \Concrete\Core\Entity\Attribute\Set $set)
    {
        $attributes= array();
        $userKeyListIDs=array_keys($this->categoryEntity->getController()->getUserKeyList($groups));
        foreach ($set->getAttributeKeys() as $attributeKey) {
            if (in_array($attributeKey->getAttributeKeyID(), $userKeyListIDs)) {
                $attributes[]=$attributeKey;
            }
        }
        return $attributes;
    }
}
