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
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getPostName()
     */
    public function getPostName($key, array $options)
    {
        return empty($options['postName']) ? "{$key}_external_url" : $options['postName'];
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
    public function generate($key, array $options, $selectedValue = null)
    {
        $miscFields = $options;
        unset($miscFields['displayName']);
        unset($miscFields['postName']);

        return $this->formService->text($this->getPostName($key, $options), $selectedValue, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::decode()
     */
    public function decode(ParameterBag $data, $key, array $options, ArrayAccess $errors = null)
    {
        $result = null;
        $postName = $this->getPostName($key, $options);
        $postValue = $data->get($postName);
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
