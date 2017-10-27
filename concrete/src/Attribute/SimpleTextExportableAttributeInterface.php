<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\Error\ErrorList;

/**
 * Attribute controllers should implement this interface if they support importing/exporting to/from plain text.
 *
 * For complex data see MulticolumnTextExportableAttributeInterface
 */
interface SimpleTextExportableAttributeInterface
{
    /**
     * Get the text representation of an attribute value.
     *
     * @param AbstractValue $value the value for which we want the text representation
     *
     * @return string
     *
     * @example A boolean attribute may return:
     * - '0' if $value is false
     * - '1' if $value is true
     * - '' if $value is not set.
     */
    public function getAttributeValueTextRepresentation(AbstractValue $value = null);

    /**
     * Update an attribute value starting from its text representation.
     *
     * @param AbstractValue $value the value to be updated
     * @param string $textRepresentation the text representation of the attribute value
     * @param ErrorList $warnings An ErrorList instance that the method can add decoding warnings to
     *
     * @example In case of booleans attributes, this may:
     * - set the attribute value to false if $textRepresentation is '0'
     * - set the attribute value to true if $textRepresentation is '1'
     * - set the attribute value to NULL if $textRepresentation is not '0' or '1'
     * - add to the $warnings instance a message $textRepresentation is not aempty and it's not '0' or '1'
     */
    public function updateAttributeValueFromTextRepresentation(AbstractValue $value, $textRepresentation, ErrorList $warnings);
}
