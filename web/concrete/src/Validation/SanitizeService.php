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
    public function sanitizeString($string)
    {
        return filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
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
