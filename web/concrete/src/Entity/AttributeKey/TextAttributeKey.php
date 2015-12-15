<?php

namespace Concrete\Core\Entity\AttributeKey;

use Concrete\Core\Entity\AttributeValue\TextAttributeValue;


/**
 * @Entity
 * @Table(name="TextAttributeKeys")
 */
class TextAttributeKey extends AttributeKey
{

    public function getTypeHandle()
    {
        return 'text';
    }

    public function getAttributeValueClass()
    {
        return new TextAttributeValue();
    }

    public function getController()
    {
        return new \Concrete\Attribute\Text\Controller($this->getAttributeType());
    }

}
