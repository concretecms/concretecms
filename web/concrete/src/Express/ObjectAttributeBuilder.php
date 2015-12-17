<?php
namespace Concrete\Core\Express;

use Concrete\Core\Application\Application;
use \Concrete\Core\Entity\Express\Entity;

class ObjectAttributeBuilder
{

    /**
     * @var \Concrete\Core\Entity\AttributeKey\AttributeKey
     */
    protected $attribute;

    protected $builder;

    public function __construct(ObjectBuilder $builder, $type, $name)
    {
        $this->builder = $builder;
        $this->attribute = $builder
            ->getAttributeTypeFactory()
            ->getByHandle($type)
            ->getController()
            ->createAttributeKey();
        $this->attribute->setAttributeKeyName($name);
    }

    public function __call($method, $args)
    {
        call_user_func_array(array(
            $this->attribute,
            $method), $args);
        return $this;
    }

    public function build()
    {
        $this->builder->getObject()->getAttributes()->add($this->attribute);
    }



}