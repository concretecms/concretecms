<?php

namespace Concrete\Core\Validation;

use Concrete\Core\Error\Error;

interface ResponseInterface
{

    /**
     * @return bool
     */
    function isValid();

    /**
     * @return Error | null
     */
    function getErrorObject();

}