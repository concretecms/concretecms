<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\TypeFactory;
use \Concrete\Core\Entity\Express\Entity;

class ObjectBuilder
{

    protected $attributeTypeFactory;
    protected $entity;

    /**
     * @return AttributeTypeFactory
     */
    public function getAttributeTypeFactory()
    {
        return $this->attributeTypeFactory;
    }

    public function __construct(TypeFactory $attributeTypeFactory)
    {
        $this->attributeTypeFactory = $attributeTypeFactory;
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
        $attribute = $this->attributeTypeFactory->getByHandle($type)->getController()->createAttributeKey();
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