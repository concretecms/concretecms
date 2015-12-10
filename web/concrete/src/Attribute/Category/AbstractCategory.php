<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\AttributeKeyFactory;
use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Key\Factory;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Concrete\Core\Entity\Express\Attribute;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCategory implements CategoryInterface
{

    protected $entityManager;
    protected $entity;
    protected $attributeKeyFactory;

    public function __construct(AttributeKeyFactory $attributeKeyFactory, EntityManager $entityManager)
    {
        $this->attributeKeyFactory = $attributeKeyFactory;
        $this->entityManager = $entityManager;
    }

    public function addFromRequest(Type $type, Request $request)
    {
        $key = $this->attributeKeyFactory->make($type->getAttributeTypeHandle());
        $loader = $key->getRequestLoader();
        $loader->load($key, $request);
        return $key;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

}
