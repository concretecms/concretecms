<?php

namespace Concrete\Core\Validation;

use Concrete\Core\Error\ErrorList\ErrorList;

/**
 * @since 8.0.0
 */
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