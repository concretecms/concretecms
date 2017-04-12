<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Category\CategoryInterface;

interface ObjectInterface
{
    function getAttributeValueObject($ak, $createIfNotExists = false);
    function getAttribute($ak, $mode = false);
    function getAttributeValue($ak);

    /**
     * @return CategoryInterface
     */
    function getObjectAttributeCategory();
    function clearAttribute($ak);
    function setAttribute($ak, $value);
}