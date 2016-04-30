<?php
namespace Concrete\Core\Search\Field;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Entity\Attribute\Key\Key;

class AttributeKeyField implements FieldInterface
{

    protected $attributeKey;

    public function getKey()
    {
        return 'attribute_key_' . $this->attributeKey->getAttributeKeyHandle();
    }

    public function getDisplayName()
    {
        return $this->attributeKey->getAttributeKeyDisplayName();
    }

    public function __construct(Key $attributeKey)
    {
        $this->attributeKey = $attributeKey;
    }
}
