<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Concrete\Core\Form\Service\Form;
use Egulias\EmailValidator\EmailValidator;
use Symfony\Component\HttpFoundation\ParameterBag;

class EmailPicker implements PickerInterface
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
        return empty($options['displayName']) ? t('Email Address') : $options['displayName'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::getPostName()
     */
    public function getPostName($key, array $options)
    {
        return empty($options['postName']) ? "{$key}_email" : $options['postName'];
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
        unset($miscFields['checkDNS']);
        unset($miscFields['strict']);

        return $this->formService->email($this->getPostName($key, $options), $selectedValue, $options);
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
                            $errors[] = t('The maximum length of the email address is %s characters.', $maxLength);
                        }
                    }
                }
                if ($postValue !== null) {
                    $emailValidator = new EmailValidator();
                    if (!$emailValidator->isValid($postValue, !empty($options['checkDNS']), !empty($options['strict']))) {
                        $postValue = null;
                        if ($errors !== null) {
                            $errors[] = t('The specified email address is not valid.');
                        }
                    }
                }
                $result = $postValue;
            }
        }

        return $result;
    }
}
