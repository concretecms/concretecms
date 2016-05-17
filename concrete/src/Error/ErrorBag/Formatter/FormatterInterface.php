<?php
namespace Concrete\Core\Error\ErrorBag\Formatter;

use Concrete\Core\Error\ErrorBag\ErrorBag;

interface FormatterInterface
{

    function __construct(ErrorBag $error);
    function output();

}
