<?php

namespace Concrete\Core\Api\Exception;

use Throwable;

class InvalidLimitQueryParameterValueException extends \Exception
{

    public function __construct($message = "", $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(t('Invalid limit query parameter found. Value must be between 1 and 100.'));
    }

}
