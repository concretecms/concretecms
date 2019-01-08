<?php

namespace Concrete\Core\File\Image\Svg;

use Concrete\Core\Error\UserMessageException;

class SanitizerException extends UserMessageException
{
    /**
     * Error code: failed to read the file.
     *
     * @var int
     */
    const ERROR_FAILED_TO_READ_FILE = 1;

    /**
     * Error code: failed to write the file.
     *
     * @var int
     */
    const ERROR_FAILED_TO_WRITE_FILE = 2;

    /**
     * Error code: failed to parse the XML.
     *
     * @var int
     */
    const ERROR_FAILED_TO_PARSE_XML = 10;

    /**
     * Error code: failed to generate the XML.
     *
     * @var int
     */
    const ERROR_FAILED_TO_GENERATE_XML = 11;

    /**
     * @param int $errorCode one of the ERROR_... constants
     * @param string $customErrorMessage A custom error message
     *
     * @return static
     */
    public static function create($errorCode, $customErrorMessage = '')
    {
        $customErrorMessage = (string) $customErrorMessage;
        if ($customErrorMessage !== null) {
            return new static($customErrorMessage, $errorCode);
        }
        switch ($errorCode) {
            case static::ERROR_FAILED_TO_READ_FILE:
                return new static(t('Failed to read the SVG file.'), $errorCode);
            case static::ERROR_FAILED_TO_WRITE_FILE:
                return new static(t('Failed to write the SVG file.'), $errorCode);
            case static::ERROR_FAILED_TO_PARSE_XML:
                return new static(t('Failed to parse the SVG file.'), $errorCode);
            case static::ERROR_FAILED_TO_GENERATE_XML:
                return new static(t('Failed to generate the XML of the SVG file.'), $errorCode);
        }
    }
}
