<?php
namespace Concrete\Core\Error\Handler;

use Whoops\Handler\PlainTextHandler as WhoopsPlainTextHandler;

class PlainTextHandler extends WhoopsPlainTextHandler
{

    protected function dump($var)
    {
        var_dump_safe($var);
    }



}
