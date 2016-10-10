<?php

namespace Concrete\Core\Http\Exception;

use Concrete\Core\Http\Response;

/**
 * Class RedirectException
 * An exception to show that the requested content is located elsewhere
 * @package Concrete\Core\Http\Exception
 */
class RedirectException extends HttpResponseException
{

    /**
     * @var array
     */
    private $headers;
    /**
     * @var string
     */
    private $to;

    /**
     * RedirectException constructor.
     * @param string $to The url to redirect to
     * @param int $code The response code, usually 302 (temporary) or 301 (permanent)
     * @param array $headers
     * @param \Exception $previous
     */
    public function __construct($to, $code = Response::HTTP_FOUND, $headers = array(), \Exception $previous = null)
    {
        parent::__construct("{$code} Redirect", $code, $headers, $previous);
        $this->headers = $headers;
        $this->to = $to;
    }

    /**
     * The url to redirect to.
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->to;
    }

}
