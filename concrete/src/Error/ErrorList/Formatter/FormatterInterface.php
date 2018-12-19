<?php

namespace Concrete\Core\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\ErrorList;

interface FormatterInterface
{
    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Error\ErrorList\ErrorList $error the error list to be formatted
     */
    public function __construct(ErrorList $error);

    /**
     * Render the error list.
     *
     * @return string
     */
    public function render();
}
