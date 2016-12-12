<?php
namespace Concrete\Core\Error\ErrorList\Field;

use Concrete\Core\Entity\Attribute\Key\Key;

class AttributeField extends AbstractField
{

    protected $key;

    public function __construct(Key $key)
    {
        $this->key = $key;

    }

    public function getFieldElementName()
    {
        return $this->key->getAttributeKeyHandle();
    }

    public function getDisplayName()
    {
        return $this->key->getAttributeKeyDisplayName();
    }

}
