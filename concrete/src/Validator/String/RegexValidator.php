<?php
namespace Concrete\Core\Validator\String;

use Concrete\Core\Validator\AbstractTranslatableValidator;

class RegexValidator extends AbstractTranslatableValidator
{
    /** Passed string doesn't match */
    const E_DOES_NOT_MATCH = 1;

    /** @var string Regex pattern */
    protected $pattern;

    /**
     * RegexValidator constructor.
     *
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;

        $this->setRequirementString(self::E_DOES_NOT_MATCH, 'Must match pattern.');
        $this->setErrorString(self::E_DOES_NOT_MATCH, function ($validator, $code, $mixed) {
            return sprintf(
                'RegexError: String \"%s\" does not match expected pattern.',
                $mixed);
        });
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
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
     * @throws \RuntimeException         Invalid regex pattern.
     */
    public function isValid($mixed, \ArrayAccess $error = null)
    {
        if (!is_string($mixed)) {
            throw new \InvalidArgumentException('\Concrete\Core\Validator\String\TooShortValidator only validates string length');
        }

        $result = @preg_match($this->getPattern(), $mixed);
        if ($result === false) {
            throw new \RuntimeException(sprintf('Regex Error: %i', preg_last_error()));
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
