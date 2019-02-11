<?php

namespace Concrete\Core\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\ErrorList;

abstract class AbstractFormatter implements FormatterInterface
{
    /**
     * The error list to be formatted.
     *
     * @var \Concrete\Core\Error\ErrorList\ErrorList
     */
    protected $error;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Error\ErrorList\ErrorList $error the error list to be formatted
     */
    public function __construct(ErrorList $error)
    {
        $this->error = $error;
    }
}
