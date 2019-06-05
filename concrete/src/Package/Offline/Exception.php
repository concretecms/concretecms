<?php

namespace Concrete\Core\Package\Offline;

use Exception as BaseException;

/**
 * Exception thrown while inspecting an offline package.
 */
class Exception extends BaseException
{
    const ERRORCODE_FILENOTFOUND = 1;
    const ERRORCODE_FILENOTREADABLE = 2;
    const ERRORCODE_BADPARAM = 3;
    const ERRORCODE_MULTIPLEPARSERSFOUND = 4;
    const ERRORCODE_CONTROLLERCLASS_NOT_FOUND = 5;
    const ERRORCODE_MISSING_OPENCURLY = 6;
    const ERRORCODE_MISSING_CLOSECURLY = 7;
    const ERRORCODE_MISSING_CLASSNAME = 8;
    const ERRORCODE_MULTIPLE_CONTROLLECLASSES = 9;
    const ERRORCODE_MISSING_NAMESPACENAME = 10;
    const ERRORCODE_INVALID_NAMESPACENAME = 11;
    const ERRORCODE_MISSING_PACKAGEHANDLE_PROPERTY = 12;
    const ERRORCODE_MISMATCH_PACKAGEHANDLE = 13;
    const ERRORCODE_INVALID_PACKAGEHANDLE = 14;
    const ERRORCODE_MISSIMG_SEMICOLON = 15;
    const ERRORCODE_UNSUPPORTED_TOKENVALUE = 16;
    const ERRORCODE_INVALID_STRING_TOKEN = 17;
    const ERRORCODE_MISSING_PACKAGEVERSION_PROPERTY = 18;
    const ERRORCODE_INVALID_PACKAGEVERSION = 19;
    const ERRORCODE_MISMATCH_PACKAGEVERSION = 20;
    const ERRORCODE_MISSING_PACKAGENAME = 21;
    const ERRORCODE_MISSING_METHOD_BODY = 22;
    const ERRORCODE_METHOD_TOO_COMPLEX = 23;
    const ERRORCODE_UNSUPPORTED_PROPERTYVALUE = 24;

    /**
     * @var mixed
     */
    protected $exceptionData;

    /**
     * @param int $code
     * @param string $message
     * @param mixed $exceptionData
     */
    public static function create($code, $message, $exceptionData = null)
    {
        $result = new static($message, $code);
        $result->exceptionData = $exceptionData;

        return $result;
    }
}
