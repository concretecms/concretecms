<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Error\ErrorList;

/**
 * Attribute controllers should implement this interface if they support importing/exporting to/from plain text.
 *
 * For complex data see MulticolumnTextExportableAttributeInterface
 */
interface SimpleTextExportableAttributeInterface
{
    /**
     * Get the text representation of the attribute value.
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
     * Create an attribute value starting from its text representation.
     *
     * @param string $textRepresentation the text representation of the attribute value
     * @param ErrorList $warnings An ErrorList instance that the method can add decoding warnings to
     *
     * @return \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue
     *
     * @example In case of booleans attributes, this may return
     * - an attribute value with false if $textRepresentation is '0'
     * - an attribute value with true if $textRepresentation is '1'
     * - an attribute value containing NULL if $textRepresentation is not '0' or '1'
     * - add to the $warnings instance a message $textRepresentation is not empty and it's not '0' or '1'
     */
    public function createAttributeValueFromTextRepresentation($textRepresentation, ErrorList $warnings);
}
