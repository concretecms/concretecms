<?php

namespace Concrete\Core\Validator;

/**
 * Class AbstractTranslatableValidator
 * Abstract class for managing translatable requirements and errors.
 *
 * @package Concrete\Core\Validator
 */
abstract class AbstractTranslatableValidator implements TranslatableValidatorInterface
{

    /**
     * @type array
     */
    protected $translatable_requirements = array();
    protected $translatable_errors = array();

    /**
     * Get an error string given a code and a passed value
     *
     * @param int   $code
     * @param mixed $value
     * @param mixed $default
     * @return string|mixed Returns a string or $default
     */
    protected function getErrorString($code, $value, $default=null)
    {
        if (array_key_exists($code, $this->translatable_errors)) {
            $resolver = $this->translatable_errors[$code];
            if ($resolver instanceof \Closure) {
                return $resolver($this, $code, $value);
            } else {
                return $resolver;
            }
        }

        return $default;
    }

    /**
     * Check to see if $value a valid stand in for a translatable string
     *
     * @param $value
     * @return bool
     */
    protected function isTranslatableStringValueValid($value)
    {
        return is_string($value) || $value instanceof \Closure;
    }

    /**
     * Set the requirement string to a mixed value
     * Closure format:
     *    function(TranslatableValidatorInterface $validator, int $code): string
     *
     * @param int $code The error code
     * @param string|\Closure $message Either a plain string, or a closure that returns a string
     * @return void
     */
    public function setRequirementString($code, $message)
    {
        if (!$this->isTranslatableStringValueValid($message)) {
            throw new \InvalidArgumentException('Invalid translatable string value provided for Validator');
        }

        $this->translatable_requirements[$code] = $message;
    }

    /**
     * Set the error string to a string or to a closure
     * Closure format:
     *    function(TranslatableValidatorInterface $validator, int $code, mixed $passed): string
     *
     * where `$passed` is whatever was passed to `ValidatorInterface::isValid`
     *
     * @param int $code The error code
     * @param string|\Closure $message Either a plain string, or a closure that returns a string
     * @return void
     */
    public function setErrorString($code, $message)
    {
        if (!$this->isTranslatableStringValueValid($message)) {
            throw new \InvalidArgumentException('Invalid translatable string value provided for Validator');
        }

        $this->translatable_errors[$code] = $message;
    }

    /**
     * Get the validator requirements in the form of an array keyed by it's respective error code
     *
     * Example:
     *    [ self::E_TOO_SHORT => 'Must be at least 10 characters' ]
     *
     * @return string[]
     */
    public function getRequirementStrings()
    {
        $map = $this->translatable_requirements;
        foreach ($map as $key => &$value) {
            if ($value instanceof \Closure) {
                $value = $value($this, $key);
            }
        }

        return $map;
    }

}
