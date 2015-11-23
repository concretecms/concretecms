<?php

namespace Concrete\Core\Entity\AttributeKey;


/**
 * @Entity
 * @Table(name="TextAttributeKeys")
 */
class TextAttributeKey extends AttributeKey
{

    public function getFieldMappingDefinition()
    {
        return array('type' => 'text', 'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false));
    }

}
