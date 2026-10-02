<?php

namespace Concrete\Core\File\Document\Xml;

use Concrete\Core\Error\UserMessageException;

/**
 * @phpstan-consistent-constructor
 */
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
        $errorMessage = (string) $customErrorMessage;
        if ($errorMessage === '') {
            switch ($errorCode) {
                case static::ERROR_FAILED_TO_READ_FILE:
                    $errorMessage = t('Failed to read the XML file.');
                    break;
                case static::ERROR_FAILED_TO_WRITE_FILE:
                    $errorMessage = t('Failed to write the XML file.');
                    break;
                case static::ERROR_FAILED_TO_PARSE_XML:
                    $errorMessage = t('Failed to parse the XML file.');
                    break;
                case static::ERROR_FAILED_TO_GENERATE_XML:
                    $errorMessage = t('Failed to generate the sanitized XML file.');
                    break;
                default:
                    $errorMessage = t('Unknown XML error (%s)', $errorCode);
                    break;
            }
        }

        return new static($errorMessage, (int) $errorCode);
    }
}