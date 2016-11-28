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

    public function getValue()
    {
        $value = $this->getAttributeValue()->getValueObject();
        if ($value) {
            return Page::getByID(intval($value));
        }
    }

    public function form()
    {
        // not used by test.

    }

}