<?php

namespace Concrete\Core\Attribute;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\Error\ErrorList;

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
     * Get the strings containing the text representation of an attribute value.
     *
     * @param AbstractValue $value the value for which we want the text representation
     *
     * @return string[]
     *
     * @example An address attribute could return ['10 Downing Street', 'London', '', 'GB']
     */
    public function getAttributeValueTextRepresentation(AbstractValue $value = null);

    /**
     * Update an attribute value starting from its text representation.
     *
     * @param AbstractValue $value the value to be updated
     * @param string[] $textRepresentation the text representation strings of the attribute value
     * @param ErrorList $warnings An ErrorList instance that the method can add decoding warnings to
     *
     * @return \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue
     *
     * @example In case of an address attribute, this may update an address attribute value with its parts extracted from $textRepresentation.
     * The method could add to the $warnings instance a message if the Country code is not valid
     */
    public function updateAttributeValueFromTextRepresentation(AbstractValue $value, array $textRepresentation, ErrorList $warnings);
}
