<?php
namespace Concrete\Core\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\ErrorList;

abstract class AbstractFormatter implements FormatterInterface
{

    protected $error;

    public function __construct(ErrorList $error)
    {
        $this->error = $error;
    }

}
