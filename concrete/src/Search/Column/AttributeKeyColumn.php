<?php

declare(strict_types=1);

namespace Concrete\Core\Search\Column;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeKeyColumn extends Column implements ColumnExportableInterface
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
                return $vo->getDisplayValue();
            }
        }
    }

    public function getColumnExportValue($obj)
    {
        if (is_object($this->attributeKey)) {
            $vo = $obj->getAttributeValueObject($this->attributeKey);
            if (is_object($vo)) {
                return $vo->getPlainTextValue();
            }
        }
    }
}
