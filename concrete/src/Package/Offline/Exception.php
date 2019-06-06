<?php

namespace Concrete\Core\Package\Offline;

use Exception as BaseException;

/**
 * Exception thrown while inspecting an offline package.
 */
class Exception extends BaseException
{
    /**
     * Error code for exceptions thrown when a directory could not be found.
     *
     * @var int
     */
    const ERRORCODE_DIRECTORYNOTFOUND = 1;

    /**
     * Error code for exceptions thrown when a file could not be found.
     *
     * @var int
     */
    const ERRORCODE_FILENOTFOUND = 2;

    /**
     * Error code for exceptions thrown when a file could not be read.
     *
     * @var int
     */
    const ERRORCODE_FILENOTREADABLE = 3;

    /**
     * Error code for exceptions thrown when a function parameter contains an invalid value.
     *
     * @var int
     */
    const ERRORCODE_BADPARAM = 4;

    /**
     * Error code for exceptions thrown when the inspector detects multiple parsers for the same package.
     *
     * @var int
     */
    const ERRORCODE_MULTIPLEPARSERSFOUND = 5;

    /**
     * Error code for exceptions thrown when the package controller class couldn't be found.
     *
     * @var int
     */
    const ERRORCODE_CONTROLLERCLASS_NOT_FOUND = 6;

    /**
     * Error code for exceptions thrown when there's a missing required open curly bracket ('{').
     *
     * @var int
     */
    const ERRORCODE_MISSING_OPENCURLY = 7;

    /**
     * Error code for exceptions thrown when there's a missing required close curly bracket ('}').
     *
     * @var int
     */
    const ERRORCODE_MISSING_CLOSECURLY = 8;

    /**
     * Error code for exceptions thrown when a class misses its name.
     *
     * @var int
     */
    const ERRORCODE_MISSING_CLASSNAME = 9;

    /**
     * Error code for exceptions thrown when multiple package controller classes are detected.
     *
     * @var int
     */
    const ERRORCODE_MULTIPLE_CONTROLLECLASSES = 10;

    /**
     * Error code for exceptions thrown when a namespace name is missing.
     *
     * @var int
     */
    const ERRORCODE_MISSING_NAMESPACENAME = 11;

    /**
     * Error code for exceptions thrown when an namespace name is not valid.
     *
     * @var int
     */
    const ERRORCODE_INVALID_NAMESPACENAME = 12;

    /**
     * Error code for exceptions thrown when a package controller class is missing the package handle property.
     *
     * @var int
     */
    const ERRORCODE_MISSING_PACKAGEHANDLE_PROPERTY = 13;

    /**
     * Error code for exceptions thrown when the handle derived from the controller fully-qualified class name differs from the value of the package handle property.
     *
     * @var int
     */
    const ERRORCODE_MISMATCH_PACKAGEHANDLE = 14;

    /**
     * Error code for exceptions thrown when the package handle contains invalid characters.
     *
     * @var int
     */
    const ERRORCODE_INVALID_PACKAGEHANDLE = 15;

    /**
     * Error code for exceptions thrown when there's a missing required semicolor (';').
     *
     * @var int
     */
    const ERRORCODE_MISSIMG_SEMICOLON = 16;

    /**
     * Error code for exceptions thrown when a PHP token value isn't supported.
     *
     * @var int
     */
    const ERRORCODE_UNSUPPORTED_TOKENVALUE = 17;

    /**
     * Error code for exceptions thrown when a PHP string token is not valid.
     *
     * @var int
     */
    const ERRORCODE_INVALID_STRING_TOKEN = 18;

    /**
     * Error code for exceptions thrown when a package controller class is missing the package version property.
     *
     * @var int
     */
    const ERRORCODE_MISSING_PACKAGEVERSION_PROPERTY = 19;

    /**
     * Error code for exceptions thrown when the package handle is malformed.
     *
     * @var int
     */
    const ERRORCODE_INVALID_PACKAGEVERSION = 20;

    /**
     * Error code for exceptions thrown when the package does not define its name.
     *
     * @var int
     */
    const ERRORCODE_MISSING_PACKAGENAME = 21;

    /**
     * Error code for exceptions thrown when a method don't have a body.
     *
     * @var int
     */
    const ERRORCODE_MISSING_METHOD_BODY = 22;

    /**
     * Error code for exceptions thrown when a method is too complex to be resolved.
     *
     * @var int
     */
    const ERRORCODE_METHOD_TOO_COMPLEX = 23;

    /**
     * Error code for exceptions thrown when a property value is of an unsupported type.
     *
     * @var int
     */
    const ERRORCODE_UNSUPPORTED_PROPERTYVALUE = 24;

    /**
     * The contextual data associated to the exception.
     *
     * @var mixed
     */
    protected $exceptionData;

    /**
     * Create a new instance of this class.
     *
     * @param int $code the error code (one of the ERRORCODE_... constants)
     * @param string $message the error message
     * @param mixed $exceptionData he contextual data associated to the exception
     */
    public static function create($code, $message, $exceptionData = null)
    {
        $result = new static($message, $code);
        $result->exceptionData = $exceptionData;

        return $result;
    }

    /**
     * Get the contextual data associated to the exception.
     *
     * @return mixed
     */
    public function getExceptionData()
    {
        return $this->exceptionData;
    }
}
