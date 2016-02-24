<?php
namespace Concrete\Core\Validator\String;

use Concrete\Core\Validator\AbstractTranslatableValidator;

/**
 * Validate the length of a string
 * @package Concrete\Core\Validator\String
 */
class MaximumLengthValidator extends AbstractTranslatableValidator
{

    /** Too long */
    const E_TOO_LONG = 1;

    /** @type int The maximum length */
    protected $maximum_length;

    /**
     * MaximumLengthValidator constructor.
     *
     * @param int $maximum_length
     */
    public function __construct($maximum_length)
    {
        $this->maximum_length = $maximum_length;

        $this->setRequirementString(self::E_TOO_LONG, function(MaximumLengthValidator$validator, $code) {

            return sprintf(
                'Must be at most %s characters long.',
                $validator->getMaximumLength());
        });

        $this->setErrorString(self::E_TOO_LONG, function(MaximumLengthValidator $validator, $code, $mixed) {
            return sprintf(
                'String \"%s\" must be at most %s characters long.',
                $mixed,
                $validator->getMaximumLength());
        });
    }

    /**
     * Get the maximum length allowed
     *
     * @return int
     */
    public function getMaximumLength()
    {
        return $this->maximum_length;
    }

    /**
     * Set the maximum length
     *
     * @param int $maximum_length
     */
    public function setMaximumLength($maximum_length)
    {
        $this->maximum_length = $maximum_length;
    }

    /**
     * Is this mixed value valid
     *
     * @param mixed             $mixed Can be any value
     * @param \ArrayAccess|null $error
     * @return bool
     * @throws \InvalidArgumentException Invalid mixed value type passed.
     */
    public function isValid($mixed, \ArrayAccess $error = null)
    {
        if (!is_string($mixed) && !is_null($mixed)) {
            throw new \InvalidArgumentException('Invalid type supplied to validator.');
        }

        if ($this->getMaximumLength() < strlen($mixed)) {
            if ($error && $message = $this->getErrorString(self::E_TOO_LONG, $mixed)) {
                $error[] = $message;
            }

            return false;
        }

        return true;
    }

}
