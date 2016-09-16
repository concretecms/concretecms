<?php
namespace Concrete\Core\Error\ErrorList\Formatter;

use Concrete\Core\Error\ErrorList\ErrorList;

interface FormatterInterface
{

    function __construct(ErrorList $error);
    function render();

}
