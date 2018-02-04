<?php

namespace Concrete\Attribute\RelatedPage;

use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;
use Concrete\Core\Page\Page;

class Controller extends \Concrete\Attribute\Number\Controller
{
    /**
     * @param $value Page
     */
    public function createAttributeValue($value)
    {
        $av = new NumberValue();
        $av->setValue($value->getCollectionID());

        return $av;
    }

    public function createAttributeValueFromRequest()
    {
        // not used by test.
    }

    public function getSearchIndexValue()
    {
        $value = $this->getValue();
        if (is_object($value)) {
            return $value->getCollectionID();
        }
    }

    public function getDisplayValue()
    {
        $value = $this->getValue();
        if (is_object($value)) {
            return $value->getCollectionName();
        }
    }

    public function getPlainTextValue()
    {
        $value = $this->getValue();
        if (is_object($value)) {
            return $value->getCollectionName();
        }
    }

    public function getValue()
    {
        $value = $this->getAttributeValue()->getValueObject();
        if ($value) {
            return Page::getByID($value->getValue());
        }
    }

    public function form()
    {
        // not used by test.
    }
}
