<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Concrete\Core\Form\Service\Form;
use Symfony\Component\HttpFoundation\ParameterBag;

class ExternalUrlPicker implements PickerInterface
{
    /**
     * @var \Concrete\Core\Form\Service\Form
     */
    protected $formService;

    public function __construct(Form $formService)
    {
        $this->formService = $formService;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getDisplayName()
     */
    public function getDisplayName(array $options)
    {
        return empty($options['displayName']) ? t('External URL') : $options['displayName'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getHeight()
     */
    public function getHeight()
    {
        return 40;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::generate()
     */
    public function generate($pickerKey, array $options, $selectedValue = null)
    {
        $miscFields = $options;
        unset($miscFields['displayName']);

        return $this->formService->text($pickerKey, $selectedValue, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::decode()
     */
    public function decode(ParameterBag $data, $pickerKey, array $options, ArrayAccess $errors = null)
    {
        $result = null;
        $postValue = $data->get($pickerKey);
        if (is_string($postValue)) {
            $postValue = trim($postValue);
            if ($postValue !== '') {
                $maxLength = empty($options['maxlength']) ? 0 : (int) $options['maxlength'];
                if ($maxLength > 0) {
                    $postLength = mb_strlen($postValue);
                    if ($postLength > $maxLength) {
                        $postValue = null;
                        if ($errors !== null) {
                            $errors[] = t('The maximum length of the external URL is %s characters.', $maxLength);
                        }
                    }
                }
                $result = $postValue;
            }
        }

        return $result;
    }
}
