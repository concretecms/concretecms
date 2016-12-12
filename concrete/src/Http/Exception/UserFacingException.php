<?php

namespace Concrete\Core\Http\Exception;

use Concrete\Core\Http\Response;
use Exception;

/**
 * Class UserFacingException
 * An exception whose message is safe to output to the user
 * @package Concrete\Core\Http\Exception
 */
class UserFacingException extends HttpResponseException
{
    /**
     * @var string
     */
    private $title;

    /**
     * UserFacingException constructor.
     * @param string $message The error message
     * @param string $title The error title
     * @param int $code The error code
     * @param array $headers
     * @param \Exception $previous
     */
    public function __construct($message, $title = null, $code = Response::HTTP_INTERNAL_SERVER_ERROR, array $headers = [], Exception $previous = null)
    {
        parent::__construct($message, $code, $headers, $previous);
        $this->title = $title;
    }

    /**
     * Get the title of the error
     * @return string
     */
    public function getTitle()
    {
        if ($this->title === null) {
            return t('An unexpected error occurred');
        }

        return $this->title;
    }

}
