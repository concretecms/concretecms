<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Symfony\Component\HttpFoundation\ParameterBag;

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
    public function decode(ParameterBag $data, $pickerKey, array $options, ArrayAccess $errors = null)
    {
        return '';
    }
}
