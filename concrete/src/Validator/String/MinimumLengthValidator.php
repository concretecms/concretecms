<?php
namespace Concrete\Core\Validator\String;

use Concrete\Core\Validator\AbstractTranslatableValidator;

/**
 * Validate the length of a string.
 *
 * \@package Concrete\Core\Validator\String
 */
class MinimumLengthValidator extends AbstractTranslatableValidator
{
    /** Too short */
    const E_TOO_SHORT = 1;

    /** @var int The minimum length */
    protected $minimum_length;

    /**
     * MinimumLengthValidator constructor.
     *
     * @param int $minimum_length
     */
    public function __construct($minimum_length)
    {
        $this->minimum_length = $minimum_length;

        $this->setRequirementString(self::E_TOO_SHORT, function (MinimumLengthValidator $validator, $code) {

            return sprintf(
                'Must be at least %s characters long.',
                $validator->getMinimumLength());
        });

        $this->setErrorString(self::E_TOO_SHORT, function (MinimumLengthValidator $validator, $code, $mixed) {
            return sprintf(
                'String \"%s\" must be at least %s characters long.',
                $mixed,
                $validator->getMinimumLength());
        });
    }

    /**
     * Get the minimum length allowed.
     *
     * @return int
     */
    public function getMinimumLength()
    {
        return $this->minimum_length;
    }

    /**
     * Set the minimum length.
     *
     * @param int $minimum_length
     */
    public function setMinimumLength($minimum_length)
    {
        $this->minimum_length = $minimum_length;
    }

    /**
     * Is this mixed value valid.
     *
     * @param mixed             $mixed Can be any value
     * @param \ArrayAccess|null $error
     *
     * @return bool
     *
     * @throws \InvalidArgumentException Invalid mixed value type passed.
     */
    public function isValid($mixed, \ArrayAccess $error = null)
    {
        if (!is_string($mixed) && !is_null($mixed)) {
            throw new \InvalidArgumentException('Invalid type supplied to validator.');
        }

        if ($this->getMinimumLength() > strlen($mixed)) {
            if ($error && $message = $this->getErrorString(self::E_TOO_SHORT, $mixed)) {
                $error[] = $message;
            }

            return false;
        }

        return true;
    }
}
