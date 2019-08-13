<?php
namespace Concrete\Core\Error\ErrorList\Field;

/**
 * @since 8.0.0
 */
abstract class AbstractField implements FieldInterface
{

    public function __toString()
    {
        return $this->getDisplayName();
    }

    public function jsonSerialize()
    {
        return ['element' => $this->getFieldElementName(), 'name' => $this->getDisplayName()];
    }


}
