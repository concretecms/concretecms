<?php

namespace Concrete\Core\Utility\Service\Validation;

use Egulias\EmailValidator\EmailValidator;

/**
 * Functions useful for validating strings
 */
class Strings
{

    /**
     * Returns true if the provided email is valid
     * @param string $em The email address to be tested
     * @param bool $testMXRecord Set to true if you want to perform dns record validation for the domain, defaults to false
     * @param bool $strict Strict email validation
     * @return bool
     */
    public function email($em, $testMXRecord = false, $strict = false)
    {
        $validator = new EmailValidator();
        return $validator->isValid($em, $testMXRecord, $strict);
    }

    /**
     * Returns true on whether the passed string is completely alpha-numeric, if the value is not a string or is an
     * empty string false will be returned.
     * @param string $value
     * @param bool $allowSpaces whether or not spaces are permitted in the field contents
     * @param bool $allowDashes whether or not dashes (-) are permitted in the field contents
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
     * Returns true if the passed string is a valid "handle" (e.g. only letters, numbers, or a _ symbol)
     * @param string $handle
     * @return bool
     */
    public function handle($handle)
    {
        return $this->notempty($handle) && !preg_match("/[^A-Za-z0-9\_]/", $handle);
    }

    /**
     * Returns false if the string is empty (including trim())
     * @param string $field
     * @return bool
     */
    public function notempty($field)
    {
        return is_string($field) && trim($field) !== '';
    }

    /**
     * Returns true on whether the passed string is larger or equal to the passed length
     * @param string $str
     * @param int $length
     * @return bool
     */
    public function min($str, $length)
    {
        return $this->notempty($str) && strlen(trim($str)) >= $length;
    }

    /**
     * Returns true on whether the passed is smaller or equal to the passed length
     * @param string $str
     * @param int $length
     * @return bool
     */
    public function max($str, $length)
    {
        return $this->notempty($str) && strlen(trim($str)) <= $length;
    }

    /**
     * Returns 0 if there are no numbers in the string, or returns the number of numbers in the string
     * @param string $str
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
     * Returns 0 if there are no upper case letters in the string, or returns the number of upper case letters in the string
     * @param string $str
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
     * Returns 0 if there are no lower case letters in the string, or returns the number of lower case letters in the string
     * @param string $str
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
     * Returns 0 if there are no symbols in the string, or returns the number of symbols in the string
     * @param string $str
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
