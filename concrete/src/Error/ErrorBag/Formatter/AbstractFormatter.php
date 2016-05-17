<?php
namespace Concrete\Core\Error\ErrorBag\Formatter;

use Concrete\Core\Error\ErrorBag\ErrorBag;

abstract class AbstractFormatter implements FormatterInterface
{

    protected $error;

    public function __construct(ErrorBag $error)
    {
        $this->error = $error;
    }

}
