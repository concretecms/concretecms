<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Concrete\Core\Entity\Page\Attribute;
use Symfony\Component\HttpFoundation\Request;

class PageCategory extends AbstractCategory
{

    /**
     * Takes an attribute key as created by the subroutine and assigns it to the page category.
     * @param Key $key
     */
    protected function assignToCategory(Key $key)
    {
        $this->entityManager->persist($key);
        $this->entityManager->flush();
        $attribute = new Attribute();
        $attribute->setAttributeKey($key);
        $this->entityManager->persist($attribute);
        $this->entityManager->flush();
    }

    public function getAttributeKeyByHandle($handle)
    {
        $query = 'select p from \Concrete\Core\Entity\Page\Attribute p join p.attribute_key a
         where a.akHandle = :handle';
        $query = $this->entityManager->createQuery($query);
        $query->setParameter('handle', $handle);
        $attribute = $query->getOneOrNullResult();
        if ($attribute) {
            return $attribute->getAttributeKey();
        }
    }

    public function addFromRequest(AttributeType $type, Request $request)
    {
        $key = parent::addFromRequest($type, $request);
        $this->assignToCategory($key);
    }

    public function import(AttributeType $type, \SimpleXMLElement $element)
    {
        $key = parent::import($type, $element);
        $this->assignToCategory($key);
    }

    public function delete(Key $key)
    {
        $query = $this->entityManager->createQuery(
            'select a from Concrete\Core\Entity\Page\Attribute a where a.attribute_key = :key'
        );
        $query->setParameter('key', $key);
        $attribute = $query->getSingleResult();
        if (is_object($attribute)) {
            $this->entityManager->remove($attribute);
            $this->entityManager->flush();
        }
    }



}
