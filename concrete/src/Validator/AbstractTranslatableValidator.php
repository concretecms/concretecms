<?php

namespace Concrete\Core\Validator;

use Closure;
use InvalidArgumentException;

/**
 * Abstract class for managing translatable requirements and errors.
 */
abstract class AbstractTranslatableValidator implements TranslatableValidatorInterface
{
    /**
     * @var array
     */
    protected $translatable_requirements = [];

    /**
     * @var array
     */
    protected $translatable_errors = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\TranslatableValidatorInterface::setRequirementString()
     */
    public function setRequirementString($code, $message)
    {
        if (!$this->isTranslatableStringValueValid($message)) {
            throw new InvalidArgumentException('Invalid translatable string value provided for Validator');
        }

        $this->translatable_requirements[$code] = $message;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\TranslatableValidatorInterface::setErrorString()
     */
    public function setErrorString($code, $message)
    {
        if (!$this->isTranslatableStringValueValid($message)) {
            throw new InvalidArgumentException('Invalid translatable string value provided for Validator');
        }

        $this->translatable_errors[$code] = $message;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Validator\ValidatorInterface::getRequirementStrings()
     */
    public function getRequirementStrings()
    {
        $map = $this->translatable_requirements;
        foreach ($map as $key => &$value) {
            if ($value instanceof Closure) {
                $value = $value($this, $key);
            }
        }

        return $map;
    }

    /**
     * Get an error string given a code and a passed value.
     *
     * @param int $code
     * @param mixed $value
     * @param mixed $default
     *
     * @return string|mixed Returns a string or $default
     */
    protected function getErrorString($code, $value, $default = null)
    {
        if (array_key_exists($code, $this->translatable_errors)) {
            $resolver = $this->translatable_errors[$code];
            if ($resolver instanceof Closure) {
                return $resolver($this, $code, $value);
            } else {
                return $resolver;
            }
        }

        return $default;
    }

    /**
     * Check to see if $value a valid stand in for a translatable string.
     *
     * @param \Closure|string|mixed $value
     *
     * @return bool
     */
    protected function isTranslatableStringValueValid($value)
    {
        return $value instanceof Closure || is_string($value);
    }
}
