<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;

/**
 * A picker for DestinationPicker that allows users specify no value.
 *
 * Supported options for the generate method:
 * - displayName: the display name of this picker (to be used in the SELECT html element).
 *
 * Supported options for the decode method:
 * - none
 */
class NoDestinationPicker implements PickerInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getDisplayName()
     */
    public function getDisplayName(array $options)
    {
        return empty($options['displayName']) ? tc('Destination', 'None') : $options['displayName'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getHeight()
     */
    public function getHeight()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::generate()
     */
    public function generate($pickerKey, array $options, $selectedValue = null)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::decode()
     */
    public function decode(array $data, $pickerKey, array $options, ArrayAccess $errors = null, $fieldDisplayName = null)
    {
        return '';
    }
}
