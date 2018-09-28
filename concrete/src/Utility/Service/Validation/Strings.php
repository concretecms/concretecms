<?php

namespace Concrete\Core\Utility\Service\Validation;

use Concrete\Core\Application\Application;
use Concrete\Core\Validator\String\EmailValidator;

/**
 * Functions useful for validating strings.
 */
class Strings
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @deprecated Use Concrete\Core\Validator\String\EmailValidator
     *
     * @param string $em
     * @param bool $testMXRecord
     * @param bool $strict
     *
     * @return bool
     */
    public function email($em, $testMXRecord = false, $strict = false)
    {
        $validator = $this->app->make(EmailValidator::class, ['testMXRecord' => $testMXRecord, 'strict' => $strict]);

        return $validator->isValid($em);
    }

    /**
     * Returns true on whether the passed string is completely alpha-numeric, if the value is not a string or is an
     * empty string false will be returned.
     *
     * @param string $value
     * @param bool $allowSpaces whether or not spaces are permitted in the field contents
     * @param bool $allowDashes whether or not dashes (-) are permitted in the field contents
     *
     * @return bool
     */
    public function alphanum($value, $allowSpaces = false, $allowDashes = false)
    {
        $allowedCharsRegex = 'A-Za-z0-9';
        if ($allowSpaces) {
            $allowedCharsRegex .= ' ';
        }
        if ($allowDashes) {
            $allowedCharsRegex .= '\-';
        }

        return $this->notempty($value) && !preg_match('/[^' . $allowedCharsRegex . ']/', $value);
    }

    /**
     * Returns true if the passed string is a valid "handle" (e.g. only letters, numbers, or a _ symbol).
     *
     * @param string $handle
     *
     * @return bool
     */
    public function handle($handle)
    {
        return $this->notempty($handle) && !preg_match("/[^A-Za-z0-9\_]/", $handle);
    }

    /**
     * Returns false if the string is empty (including trim()).
     *
     * @param string $field
     *
     * @return bool
     */
    public function notempty($field)
    {
        return is_string($field) && trim($field) !== '';
    }

    /**
     * Returns true on whether the passed string is larger or equal to the passed length.
     *
     * @param string $str
     * @param int $length
     *
     * @return bool
     */
    public function min($str, $length)
    {
        return $this->notempty($str) && strlen(trim($str)) >= $length;
    }

    /**
     * Returns true on whether the passed is smaller or equal to the passed length.
     *
     * @param string $str
     * @param int $length
     *
     * @return bool
     */
    public function max($str, $length)
    {
        return $this->notempty($str) && strlen(trim($str)) <= $length;
    }

    /**
     * Returns 0 if there are no numbers in the string, or returns the number of numbers in the string.
     *
     * @param string $str
     *
     * @return int
     */
    public function containsNumber($str)
    {
        if (!$this->notempty($str)) {
            return 0;
        }

        return strlen(preg_replace('/([^0-9]*)/', '', $str));
    }

    /**
     * Returns 0 if there are no upper case letters in the string, or returns the number of upper case letters in the string.
     *
     * @param string $str
     *
     * @return int
     */
    public function containsUpperCase($str)
    {
        if (!$this->notempty($str)) {
            return 0;
        }

        return strlen(preg_replace('/([^A-Z]*)/', '', $str));
    }

    /**
     * Returns 0 if there are no lower case letters in the string, or returns the number of lower case letters in the string.
     *
     * @param string $str
     *
     * @return int
     */
    public function containsLowerCase($str)
    {
        if (!$this->notempty($str)) {
            return 0;
        }

        return strlen(preg_replace('/([^a-z]*)/', '', $str));
    }

    /**
     * Returns 0 if there are no symbols in the string, or returns the number of symbols in the string.
     *
     * @param string $str
     *
     * @return int
     */
    public function containsSymbol($str)
    {
        if (!$this->notempty($str)) {
            return 0;
        }

        return strlen(trim(preg_replace('/([a-zA-Z0-9]*)/', '', $str)));
    }
}
