<?php
namespace Concrete\Core\Attribute;

interface AttributeValueInterface
{
    public function getAttributeTypeObject();

    /**
     * @return \Concrete\Core\Entity\Attribute\Key\Key
     */
    public function getAttributeKey();
    public function getValue($mode = false);

    /**
     * @return \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue|\Concrete\Core\Attribute\AttributeValueInterface|null
     */
    public function getValueObject();
    public function getController();
}
