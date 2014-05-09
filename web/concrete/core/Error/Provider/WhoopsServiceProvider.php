<?php
namespace Concrete\Core\Error\Provider;

use Concrete\Core\Error\Handler\ErrorHandler;
use Concrete\Core\Foundation\Service\Provider;
use Whoops\Run;

class WhoopsServiceProvider extends Provider
{

    public function register()
    {
        $run     = new Run;
        $handler = new ErrorHandler();

        $run->pushHandler($handler);
        $run->register();
    }

}
