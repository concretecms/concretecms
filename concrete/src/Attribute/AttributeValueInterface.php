<?php
namespace Concrete\Core\Attribute;

interface AttributeValueInterface
{
    public function getAttributeTypeObject();
    public function getAttributeKey();
    public function getValue($mode = false);
    public function getValueObject();
    public function getController();
}
