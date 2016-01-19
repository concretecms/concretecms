<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Express\Attribute;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ExpressCategory extends AbstractCategory
{
    public function createAttributeKey()
    {
        return new Key();
    }

    public function getAttributeRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Express\Attribute');
    }

    public function getAttributeSets()
    {
        return array();
    }

    public function allowAttributeSets()
    {
        return false;
    }

    public function getAttributeTypes()
    {
        return $this->entityManager
            ->getRepository('\Concrete\Core\Entity\Attribute\Type')
            ->findAll();
    }


    public function addFromRequest(Type $type, Request $request)
    {
        $key = parent::addFromRequest($type, $request);

        // Take our newly minted TextAttributeKey, SelectAttributeKey, etc... and pass it to the
        // category so it can be properly assigned in whatever way the category chooses to do so

        $attribute = new Attribute();
        $attribute->setAttributeKey($key);
        $attribute->setEntity($this->getEntity());
        $this->entity->getAttributes()->add($attribute);
        $this->entityManager->persist($this->getEntity());
        $this->entityManager->flush();
        return $key;
    }

    public function getAttributeValues($mixed)
    {
        // TODO: Implement getAttributeValues() method.
    }

    public function getSearchIndexer()
    {
        return false;
    }

    public function getAttributeValue(Key $key, $mixed)
    {
        // TODO: Implement getAttributeValue() method.
    }
}
