<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Error\ErrorList\ErrorList;

/**
 * Attribute controllers should implement this interface if they support importing/exporting to/from multiple plain text fields.
 *
 * For simple data see SimpleTextExportableAttributeInterface
 */
interface MulticolumnTextExportableAttributeInterface
{
    /**
     * Get the handles of the columns that will contain parts of attribute values.
     * The result must be independent of the specific attribute value instance.
     *
     * @return string[]
     *
     * @example An address attribute could return ['street', 'city', 'state', 'country']
     */
    public function getAttributeTextRepresentationHeaders();

    /**
     * Get the strings containing the text representation of the attribute value currently set in the controller.
     *
     * @return string[]
     *
     * @example An address attribute could return ['10 Downing Street', 'London', '', 'GB']
     */
    public function getAttributeValueTextRepresentation();

    /**
     * Update or create the attribute value starting from its text representation.
     *
     * @param string[] $textRepresentation the text representation strings of the attribute value
     * @param ErrorList $warnings An ErrorList instance that the method can add decoding warnings to
     *
     * @return \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue|null Returns NULL if the controller doesn't have already a value and $textRepresentation is empty
     *
     * @example In case of an address attribute, this may update an address attribute value with its parts extracted from $textRepresentation.
     * The method could add to the $warnings instance a message if the Country code is not valid.
     * The method returns NULL if there wasn't an attribute value and $textRepresentation contains only empty strings.
     */
    public function updateAttributeValueFromTextRepresentation(array $textRepresentation, ErrorList $warnings);
}
