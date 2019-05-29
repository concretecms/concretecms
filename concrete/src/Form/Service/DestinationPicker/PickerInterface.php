<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;

interface PickerInterface
{
    /**
     * Get the display name of this picker (to be used in the SELECT html element).
     *
     * @param array $options
     *
     * @return string
     */
    public function getDisplayName(array $options);

    /**
     * Get the height of this picker (in pixels).
     *
     * @return int
     */
    public function getHeight();

    /**
     * Generate the HTML for the picker.
     *
     * @param string $pickerKey
     * @param array $options
     * @param mixed|null $selectedValue
     *
     * @return string
     */
    public function generate($pickerKey, array $options, $selectedValue = null);

    /**
     * Decode the value received via post.
     *
     * @param array $data
     * @param string $pickerKey
     * @param array $options
     * @param \ArrayAccess $errors
     * @param string|null $fieldDisplayName
     */
    public function decode(array $data, $pickerKey, array $options, ArrayAccess $errors = null, $fieldDisplayName = null);
}
