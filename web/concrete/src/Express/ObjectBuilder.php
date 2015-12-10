<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeKeyFactory;
use Concrete\Core\Attribute\AttributeKeyFactoryInterface;
use \Concrete\Core\Entity\Express\Entity;

class ObjectBuilder
{

    protected $attributeKeyFactory;
    protected $entity;

    /**
     * @return AttributeKeyFactory
     */
    public function getAttributeKeyFactory()
    {
        return $this->attributeKeyFactory;
    }

    public function __construct(AttributeKeyFactory $attributeKeyFactory)
    {
        $this->attributeKeyFactory = $attributeKeyFactory;
    }

    public function createObject($name)
    {
        $this->entity = new Entity();
        $this->entity->setName($name);
        return $this;
    }

    public function createAttribute($type, $name)
    {
        return new ObjectAttributeBuilder($this, $type, $name);
    }

    public function getName()
    {
        return $this->entity->getName();
    }

    public function setName($name)
    {
        $this->entity->setName($name);
        return $this;
    }

    public function addAttribute($type, $name)
    {
        /** @var $attribute \Concrete\Core\Entity\AttributeKey\AttributeKey */
        $attribute = $this->attributeKeyFactory->make($type);
        $attribute->setAttributeKeyName($name);
        $this->entity->getAttributes()->add($attribute);
        return $this;
    }

    public function getObject()
    {
        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function buildObject()
    {
        $entity = $this->getObject();
        $this->entity = null;
        return $entity;
    }


}