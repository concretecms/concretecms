<?php

namespace Concrete\Core\Validation;

use Concrete\Core\Error\ErrorList\ErrorList;

interface ResponseInterface
{

    /**
     * @return bool
     */
    function isValid();

    /**
     * @return ErrorList | null
     */
    function getErrorObject();

}