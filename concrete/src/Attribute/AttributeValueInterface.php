<?php
namespace Concrete\Core\Attribute;

/**
 * @since 8.0.0
 */
interface AttributeValueInterface
{
    public function getAttributeTypeObject();
    public function getAttributeKey();
    public function getValue($mode = false);
    public function getValueObject();
    public function getController();
}
