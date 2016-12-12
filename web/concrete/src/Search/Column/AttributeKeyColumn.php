<?php
namespace Concrete\Core\Search\Column;

use Loader;

class AttributeKeyColumn extends Column
{
    protected $attributeKey = false;

    public function getAttributeKey()
    {
        return $this->attributeKey;
    }

    public function __construct($attributeKey, $isSortable = true, $defaultSort = 'asc')
    {
        $this->attributeKey = $attributeKey;
        parent::__construct('ak_' . $attributeKey->getAttributeKeyHandle(), $attributeKey->getAttributeKeyDisplayName(), false, $isSortable, $defaultSort);
    }

    public function getColumnValue($obj)
    {
        if (is_object($this->attributeKey)) {
            $vo = $obj->getAttributeValueObject($this->attributeKey);
            if (is_object($vo)) {
                return $vo->getValue('display');
            }
        }
    }
}
