<?php

namespace Concrete\Core\Attribute;

/**
 * Attribute controllers should implement this interface to customize the X-Editable options.
 */
interface XEditableConfigurableAttributeInterface
{
    /**
     * Return custom options for the X-Editable element.
     *
     * @see https://vitalets.github.io/x-editable/docs.html#editable
     *
     * @return array
     */
    public function getXEditableOptions();
}
