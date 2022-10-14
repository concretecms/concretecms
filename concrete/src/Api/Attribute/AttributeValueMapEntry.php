<?php

namespace Concrete\Core\Api\Attribute;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;

class AttributeValueMapEntry
{

    /**
     * @var Key
     */
    protected $attributeKey;

    /**
     * @var AbstractValue
     */
    protected $attributeValue;

    /**
     * AttributeValueMapEntry constructor.
     * @param Key $attributeKey
     * @param AbstractValue $attributeValue
     */
    public function __construct(Key $attributeKey, AbstractValue $attributeValue)
    {
        $this->attributeKey = $attributeKey;
        $this->attributeValue = $attributeValue;
    }

    /**
     * @return Key
     */
    public function getAttributeKey(): Key
    {
        return $this->attributeKey;
    }


    /**
     * @param Key $attributeKey
     */
    public function setAttributeKey(Key $attributeKey): void
    {
        $this->attributeKey = $attributeKey;
    }

    /**
     * @return AbstractValue
     */
    public function getAttributeValue(): AbstractValue
    {
        return $this->attributeValue;
    }

    /**
     * @param AbstractValue $attributeValue
     */
    public function setAttributeValue(AbstractValue $attributeValue): void
    {
        $this->attributeValue = $attributeValue;
    }



}