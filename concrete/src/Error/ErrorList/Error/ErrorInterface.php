<?php

namespace Concrete\Core\Error\ErrorList\Error;

use JsonSerializable;

interface ErrorInterface extends JsonSerializable
{
    /**
     * Get the error message.
     *
     * @return string
     */
    public function getMessage();
}
