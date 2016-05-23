<?php
namespace Concrete\Core\Error\ErrorBag\Field;

interface FieldInterface extends \JsonSerializable
{

    function getDisplayName();
    function getFieldElementName();

}
