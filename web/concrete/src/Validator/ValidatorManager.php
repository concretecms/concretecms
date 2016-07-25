<?php
namespace Concrete\Core\Validator;

class ValidatorManager implements ValidatorManagerInterface
{
    /** @var ValidatorInterface[] */
    protected $validators = array();

    /**
     * Get the validator requirements in the form of an array keyed by it's respective error code.
     *
     * Example:
     *    [ self::E_TOO_SHORT => 'Must be at least 10 characters' ]
     *
     * @return string[]
     */
    public function getRequirementStrings()
    {
        $strings = array();
        foreach ($this->getValidators() as $validator) {
            $validator_strings = $validator->getRequirementStrings();
            $strings = array_merge($strings, $validator_strings);
        }

        return $strings;
    }

    /**
     * Get a list of all validators.
     *
     * @return ValidatorInterface[] Array of validators keyed by their handles
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * Does a validator with this handle exist.
     *
     * @param string $handle
     *
     * @return bool
     */
    public function hasValidator($handle)
    {
        return isset($this->validators[$handle]);
    }

    /**
     * Add a validator to the stack.
     * Validators are unique by handle, so adding a validator with the same handle as a validator in the stack
     * replaces the old validator with the new one.
     *
     * @param string $handle
     * @param \Concrete\Core\Validator\ValidatorInterface $validator
     */
    public function setValidator($handle, ValidatorInterface $validator = null)
    {
        $this->validators[$handle] = $validator;
    }

    /**
     * Is this mixed value valid based on the added validators.
     *
     * @param mixed             $mixed Can be any value
     * @param \ArrayAccess|null $error The error object that will contain the error strings
     *
     * @return bool
     *
     * @throws \InvalidArgumentException Invalid mixed value type passed.
     */
    public function isValid($mixed, \ArrayAccess $error = null)
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
