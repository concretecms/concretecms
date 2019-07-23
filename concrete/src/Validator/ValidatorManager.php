<?php

namespace Concrete\Core\Validator;

use ArrayAccess;

/**
 * A generic validator manager interface that enables validating against many validators at once.
 */
class ValidatorManager implements ValidatorManagerInterface
{
    /**
     * The list of registered validators.
     *
     * @var \Concrete\Core\Validator\ValidatorInterface[]
     */
    protected $validators = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::getRequirementStrings()
     */
    public function getRequirementStrings()
    {
        $strings = [];
        foreach ($this->getValidators() as $validator) {
            $validator_strings = $validator->getRequirementStrings();
            $strings = array_merge($strings, $validator_strings);
        }

        return $strings;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorManagerInterface::getValidators()
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorManagerInterface::hasValidator()
     */
    public function hasValidator($handle)
    {
        return isset($this->validators[$handle]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorManagerInterface::setValidator()
     */
    public function setValidator($handle, ValidatorInterface $validator = null)
    {
        $this->validators[$handle] = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        $valid = true;
        foreach ($this->getValidators() as $validator) {
            if (!$validator->isValid($mixed, $error)) {
                $valid = false;
            }
        }

        return $valid;
    }
}
