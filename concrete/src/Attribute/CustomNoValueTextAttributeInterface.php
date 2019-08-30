<?php

namespace Concrete\Core\Attribute;

/**
 * Attribute controllers should implement this interface to customize the display value when there's no current value.
 */
interface CustomNoValueTextAttributeInterface
{
    /**
     * Return the text (in HTML format) to be displayed when there's no current value.
     *
     * @return string
     */
    public function getNoneTextDisplayValue();
}
