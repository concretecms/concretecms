<?php

namespace Concrete\Core\Api\Exception;

use Concrete\Core\Error\UserMessageHttpException;
use Throwable;

class InvalidLimitQueryParameterValueException extends UserMessageHttpException
{

    public function __construct($message = "", $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(t('Invalid limit query parameter found. Value must be between 1 and 100.'), 400, $previous);
    }

}
