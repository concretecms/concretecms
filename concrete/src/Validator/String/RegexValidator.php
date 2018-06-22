<?php

namespace Concrete\Core\Validator\String;

use ArrayAccess;
use Concrete\Core\Validator\AbstractTranslatableValidator;
use InvalidArgumentException;
use RuntimeException;

class RegexValidator extends AbstractTranslatableValidator
{
    /**
     * Passed string doesn't match.
     *
     * @var int
     */
    const E_DOES_NOT_MATCH = 1;

    /**
     * Regex pattern.
     *
     * @var string
     */
    protected $pattern;

    /**
     * RegexValidator constructor.
     *
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
        $this->setRequirementString(
            self::E_DOES_NOT_MATCH,
            t('Must match pattern.')
        );
        $this->setErrorString(
            self::E_DOES_NOT_MATCH,
            function ($validator, $code, $mixed) {
                return t('RegexError: String "%s" does not match expected pattern.', $mixed);
            }
        );
    }

    /**
     * Get the regex pattern.
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set the regex pattern.
     *
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::isValid()
     *
     * @throws \RuntimeException invalid regex pattern
     */
    public function isValid($mixed, ArrayAccess $error = null)
    {
        if (!is_string($mixed)) {
            throw new InvalidArgumentException(t(/*i18n: %s is the name of a PHP class*/'%s only validates string length', __CLASS__));
        }

        $result = @preg_match($this->getPattern(), $mixed);
        if ($result === false) {
            throw new RuntimeException(sprintf('Regex Error: %i', preg_last_error()));
        }

        if (!$result) {
            if ($error && $message = $this->getErrorString(self::E_DOES_NOT_MATCH, $mixed)) {
                $error[] = $message;
            }

            return false;
        }

        return true;
    }
}
