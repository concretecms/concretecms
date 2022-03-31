<?php

/**
 * Helper class for sanitizing input and escaping output.
 *
 * \@package Helpers
 *
 * @category Concrete
 *
 * @subpackage Security
 *
 * @author Chris Rosser <chris@bluefuton.com>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
namespace Concrete\Core\Validation;

class SanitizeService
{
    /**
     * Remove everything between < and > (or from < to the end of the string).
     *
     * @param string|mixed $string
     *
     * @return string|false return false if $string is not a scalar (or not an object with a __toString method), the sanitized string otherwise
     */
    public function sanitizeString($string)
    {
        if (is_object($string) && method_exists($string, '__toString')) {
            $string = (string) $string;
        } elseif (is_scalar($string) || $string === null) {
            $string = (string) $string;
        } else {
            return false;
        }
        $string = preg_replace('/<.*?>/ms', '', $string);
        $string = preg_replace('/<.*/ms', '', $string);

        return $string;
    }

    public function sanitizeInt($int)
    {
        return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }

    public function sanitizeURL($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    public function sanitizeEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
