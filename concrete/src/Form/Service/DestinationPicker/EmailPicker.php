<?php

namespace Concrete\Core\Form\Service\DestinationPicker;

use ArrayAccess;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Validator\String\EmailValidator;

/**
 * A picker for DestinationPicker that allows users specify an email address.
 *
 * Supported options for the generate method:
 * - displayName: the display name of this picker (to be used in the SELECT html element).
 * - any other options (except the ones used for the "decode" method) will be used to create the INPUT html element
 *
 * Supported options for the decode method:
 * - checkDNS: should we check if the email address has a valid MX record associated to the domain?
 * - strict: should validation warnings be considered as errors?
 * - maxlength: the maximum length of the email address
 */
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
        unset($miscFields['checkDNS']);
        unset($miscFields['strict']);

        return $this->formService->email($pickerKey, $selectedValue, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Form\Service\DestinationPicker\PickerInterface::decode()
     */
    public function decode(array $data, $pickerKey, array $options, ArrayAccess $errors = null, $fieldDisplayName = null)
    {
        $result = null;
        $postValue = array_get($data, $pickerKey);
        if (is_string($postValue)) {
            $postValue = trim($postValue);
            if ($postValue !== '') {
                $maxLength = empty($options['maxlength']) ? 0 : (int) $options['maxlength'];
                if ($maxLength > 0) {
                    $postLength = mb_strlen($postValue);
                    if ($postLength > $maxLength) {
                        $postValue = null;
                        if ($errors !== null) {
                            if ((string) $fieldDisplayName === '') {
                                $errors[] = t('The maximum length of the email address is %s characters.', $maxLength);
                            } else {
                                $errors[] = t('The maximum length of %1$s is %2$s characters.', $fieldDisplayName, $maxLength);
                            }
                        }
                    }
                }
                if ($postValue !== null) {
                    $emailValidator = new EmailValidator(!empty($options['checkDNS']), !empty($options['strict']));
                    if (!$emailValidator->isValid($postValue)) {
                        $postValue = null;
                        if ($errors !== null) {
                            if ((string) $fieldDisplayName === '') {
                                $errors[] = t('The specified email address is not valid.');
                            } else {
                                $errors[] = t('Please specify a valid email address for %s.', $fieldDisplayName);
                            }
                        }
                    }
                }
                $result = $postValue;
            }
        }

        return $result;
    }
}
