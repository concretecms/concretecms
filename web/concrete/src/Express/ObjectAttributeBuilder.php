<?php
namespace Concrete\Core\Express;

use Concrete\Core\Entity\Attribute\Key\Key;

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

        $type = $builder
            ->getAttributeTypeFactory()
            ->getByHandle($type);

        $key_type = $type->getController()
            ->createAttributeKeyType();

        $key_type->setAttributeType($type);

        $key = new Key();
        $key->setAttributeKeyType($key_type);
        $this->attribute = $key;

        $this->attribute->setAttributeKeyName($name);
    }

    public function __call($method, $args)
    {
        if (method_exists($this->attribute, $method)) {
            call_user_func_array(array(
                $this->attribute,
                $method), $args);
        } else if (method_exists($this->attribute->getAttributeKeyType(), $method)) {
            call_user_func_array(array(
                $this->attribute->getAttributeKeyType(),
                $method), $args);
        }

        return $this;
    }

    public function build()
    {
        $this->builder->getObject()->getAttributes()->add($this->attribute);
    }
}
