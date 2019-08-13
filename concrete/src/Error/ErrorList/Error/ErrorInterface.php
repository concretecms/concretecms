<?php

namespace Concrete\Core\Error\ErrorList\Error;

use JsonSerializable;

/**
 * @since 8.0.0
 */
interface ErrorInterface extends JsonSerializable
{
    /**
     * Get the error message.
     *
     * @return string
     */
    public function getMessage();
}
