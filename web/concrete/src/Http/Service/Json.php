<?php
namespace Concrete\Core\Http\Service;
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions for working with JSON (JavaScript Object Notation)
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Json
{
    /**
     * Decodes a JSON string into a php variable
     * @param string $string
     * @param bool $assoc [default: false] When true, returned objects will be converted into associative arrays, when false they'll be converted into stdClass instances.
     * @param int $depth [default: 512] User specified recursion depth.
     * @param int $options [default: 0] Bitmask of JSON decode options (used from PHP 5.4).
     * @return mixed
     * @link http://php.net/manual/function.json-decode.php
     */
    public function decode($string, $assoc = false, $depth = 512, $options = 0)
    {
        if ($options && (version_compare(PHP_VERSION, '5.4') >= 0)) {
            return json_decode($string, $assoc, $depth, $options);
        } else {
            return json_decode($string, $assoc, $depth);
        }
    }

    /**
     * Encodes a data structure into a JSON string
     * @param mixed $mixed
     * @param int $options [default: 0] Bitmask of JSON encode options.
     * @param int $depth [default: 512] User specified recursion depth (used from PHP 5.5).
     * @return string
     * @link http://php.net/manual/function.json-encode.php
     */
    public function encode($mixed, $options = 0, $depth = 512)
    {
        if (($depth != 512) && (version_compare(PHP_VERSION, '5.5') >= 0)) {
            return json_encode($mixed, $options, $depth);
        } else {
            return json_encode($mixed, $options);
        }
    }

}
