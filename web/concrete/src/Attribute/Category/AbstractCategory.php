<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\AttributeKeyFactory;
use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Key\Factory;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\AttributeKey\AttributeKey;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCategory implements CategoryInterface
{

    protected $entityManager;
    protected $entity;
    protected $categoryEntity;
    protected $attributeKeyFactory;

    abstract public function getByHandle($handle);

    public function __construct(AttributeKeyFactory $attributeKeyFactory, EntityManager $entityManager)
    {
        $this->attributeKeyFactory = $attributeKeyFactory;
        $this->entityManager = $entityManager;
    }

    // Create
    public function addFromRequest(Type $type, Request $request)
    {
        $key = $this->attributeKeyFactory->make($type->getAttributeTypeHandle());
        $loader = $key->getRequestLoader();
        $loader->load($key, $request);
        return $key;
    }

    public function import(AttributeType $type, \SimpleXMLElement $element)
    {
        $key = $this->attributeKeyFactory->make($type->getAttributeTypeHandle());
        $loader = $key->getImportLoader();
        $loader->load($key, $element);
        return $key;
    }


    // Update
    public function updateFromRequest(AttributeKey $key, Request $request)
    {
        $loader = $key->getRequestLoader();
        $loader->load($key, $request);
        return $key;
    }


    /**
     * @return mixed
     */
    public function getCategoryEntity()
    {
        return $this->categoryEntity;
    }

    /**
     * @param mixed $categoryEntity
     */
    public function setCategoryEntity(Category $categoryEntity)
    {
        $this->categoryEntity = $categoryEntity;
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

    public function delete(AttributeKey $key)
    {
        $this->entityManager->remove($key);
    }

    public function associateAttributeKeyType(AttributeType $type)
    {
        $this->getCategoryEntity()->getAttributeTypes()->add($type);
        $this->entityManager->persist($this->getCategoryEntity());
        $this->entityManager->flush();
    }

}
