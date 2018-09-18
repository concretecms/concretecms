<?php

namespace Concrete\Core\Validator\String;

use ArrayAccess;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use InvalidArgumentException;

/**
 * Validate the length of a string.
 */
class MinimumLengthValidator extends AbstractTranslatableValidator
{
    /**
     * Too short.
     *
     * @var int
     */
    const E_TOO_SHORT = 1;

    /**
     * The minimum length.
     *
     * @var int
     */
    protected $minimum_length;

    /**
     * MinimumLengthValidator constructor.
     *
     * @param int $minimum_length
     */
    public function __construct($minimum_length)
    {
        $this->minimum_length = $minimum_length;
        $this->setRequirementString(
            self::E_TOO_SHORT,
            function (MinimumLengthValidator $validator, $code) {
                return t('Must be at least %s characters long.', $validator->getMinimumLength());
            }
        );
        $this->setErrorString(
            self::E_TOO_SHORT,
            function (MinimumLengthValidator $validator, $code, $mixed) {
                return t('String "%s" must be at least %s characters long.', $mixed, $validator->getMinimumLength());
            }
        );
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
     * Set the minimum length allowed.
     *
     * @param int $minimum_length
     */
    public function setMinimumLength($minimum_length)
    {
        $this->minimum_length = $minimum_length;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        if ($mixed !== null && !is_string($mixed)) {
            throw new InvalidArgumentException(t('Invalid type supplied to validator.'));
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
