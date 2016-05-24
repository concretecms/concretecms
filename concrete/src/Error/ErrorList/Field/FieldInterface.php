<?php
namespace Concrete\Core\Error\ErrorList\Field;

interface FieldInterface extends \JsonSerializable
{

    function getDisplayName();
    function getFieldElementName();

}
