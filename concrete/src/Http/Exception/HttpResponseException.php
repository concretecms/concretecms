<?php

namespace Concrete\Core\Http\Exception;

/**
 * Class HttpResponseException
 * An http exception that is meant to generate a specific response
 *
 * This class is abstract because it looks great to throw but it's probably not what you want.
 * @package Concrete\Core\Http\Exception
 */
abstract class HttpResponseException extends HttpException
{
    /**
     * @var array
     */
    private $headers;

    /**
     * HttpResponseException constructor.
     * @param string $message
     * @param int $code
     * @param array $headers
     * @param \Exception|null $previous
     */
    public function __construct($message, $code, $headers = [], \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->headers = $headers;
    }

    /**
     * Get the response status code
     * @return int
     */
    public function getStatus()
    {
        return $this->getCode();
    }

    /**
     * Get the attached headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

}
