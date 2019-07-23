<?php

namespace Concrete\Core\Validator\String;

use ArrayAccess;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use InvalidArgumentException;

/**
 * Validate the length of a string.
 */
class MaximumLengthValidator extends AbstractTranslatableValidator
{
    /**
     * Too long.
     *
     * @var int
     */
    const E_TOO_LONG = 1;

    /**
     * The maximum length.
     *
     * @var int
     */
    protected $maximum_length;

    /**
     * MaximumLengthValidator constructor.
     *
     * @param int $maximum_length
     */
    public function __construct($maximum_length)
    {
        $this->maximum_length = $maximum_length;
        $this->setRequirementString(
            self::E_TOO_LONG,
            function (MaximumLengthValidator $validator, $code) {
                return t('Must be at most %s characters long.', $validator->getMaximumLength());
            }
        );
        $this->setErrorString(
            self::E_TOO_LONG,
            function (MaximumLengthValidator $validator, $code, $mixed) {
                return t('String "%s" must be at most %s characters long.', $mixed, $validator->getMaximumLength());
            }
        );
    }

    /**
     * Get the maximum length allowed.
     *
     * @return int
     */
    public function getMaximumLength()
    {
        return $this->maximum_length;
    }

    /**
     * Set the maximum length allowed.
     *
     * @param int $maximum_length
     */
    public function setMaximumLength($maximum_length)
    {
        $this->maximum_length = $maximum_length;
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

        if ($this->getMaximumLength() < strlen($mixed)) {
            if ($error && $message = $this->getErrorString(self::E_TOO_LONG, $mixed)) {
                $error[] = $message;
            }

            return false;
        }

        return true;
    }
}
