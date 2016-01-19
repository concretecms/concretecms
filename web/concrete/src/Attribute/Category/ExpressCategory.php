<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Controller\SinglePage\Dashboard\Express;
use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ExpressCategory extends AbstractCategory
{

    public function __construct(Entity $entity, Application $application, EntityManager $entityManager)
    {
        $this->setEntity($entity);
        parent::__construct($application, $entityManager);
    }

    public function createAttributeKey()
    {
        return new ExpressKey();
    }

    public function getAttributeRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\ExpressKey');
    }

    public function getAttributeSets()
    {
        return array();
    }

    public function allowAttributeSets()
    {
        return false;
    }

    public function getList()
    {
        return $this->getAttributeRepository()->findBy(array('entity' => $this->getEntity()));
    }

    public function getUnassignedAttributeKeys()
    {
        return $this->getList();
    }

    public function getAttributeTypes()
    {
        return $this->entityManager
            ->getRepository('\Concrete\Core\Entity\Attribute\Type')
            ->findAll();
    }


    public function addFromRequest(Type $type, Request $request)
    {
        /**
         * @var $key ExpressKey
         */
        $key = parent::addFromRequest($type, $request);
        $key->setEntity($this->getEntity());
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
