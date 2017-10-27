<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Error\ErrorList\ErrorList;

/**
 * Attribute controllers should implement this interface if they support importing/exporting to/from plain text.
 *
 * For complex data see MulticolumnTextExportableAttributeInterface
 */
interface SimpleTextExportableAttributeInterface
{
    /**
     * Get a string containing the text representation of the attribute value currently set in the controller.
     *
     * @return string
     *
     * @example A boolean attribute may return:
     * - '0' if $value is false
     * - '1' if $value is true
     * - '' if $value is not set.
     */
    public function getAttributeValueTextRepresentation();

    /**
     * Update or create the attribute value starting from its text representation.
     *
     * @param string $textRepresentation the text representation of the attribute value
     * @param ErrorList $warnings An ErrorList instance that the method can add decoding warnings to
     *
     * @return \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue|null Returns NULL if the controller doesn't have already a value and $textRepresentation is empty
     *
     * @example In case of booleans attributes, this may:
     * - set the attribute value to false if $textRepresentation is '0' (if the value does not exist, the value is created)
     * - set the attribute value to true if $textRepresentation is '1' (if the value does not exist, the value is created)
     * - set the attribute value to NULL if $textRepresentation is not '0' or '1'  (if the value does not exist, the value is NOT created)
     * - add to the $warnings instance a message $textRepresentation is not empty and it's not '0' or '1'
     */
    public function updateAttributeValueFromTextRepresentation($textRepresentation, ErrorList $warnings);
}
