<?php

namespace Concrete\Core\Http\Exception;

use Concrete\Core\Http\Response;

/**
 * Class ForbiddenException
 * An exception to show that the requestor does not have permission to view the requested content
 * @package Concrete\Core\Http\Exception
 */
class ForbiddenException extends HttpResponseException
{

    public function __construct($message, array $headers = [], \Exception $previous = null)
    {
        parent::__construct($message, Response::HTTP_FORBIDDEN, $headers, $previous);
    }

}
