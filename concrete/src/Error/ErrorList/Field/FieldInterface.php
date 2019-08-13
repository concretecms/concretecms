<?php
namespace Concrete\Core\Error\ErrorList\Field;

/**
 * @since 8.0.0
 */
interface FieldInterface extends \JsonSerializable
{

    function getDisplayName();
    function getFieldElementName();

}
