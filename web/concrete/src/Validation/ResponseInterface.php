<?php

namespace Concrete\Core\Validation;

use Concrete\Core\Error\ErrorBag\ErrorBag;

interface ResponseInterface
{

    /**
     * @return bool
     */
    function isValid();

    /**
     * @return ErrorBag | null
     */
    function getErrorObject();

}