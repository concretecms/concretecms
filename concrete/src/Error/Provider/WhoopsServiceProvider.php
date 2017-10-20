<?php
namespace Concrete\Core\Error\Provider;

use Concrete\Core\Error\Handler\ErrorHandler;
use Concrete\Core\Error\Handler\JsonErrorHandler;
use Concrete\Core\Foundation\Service\Provider;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;
use Whoops\Util\Misc;

class WhoopsServiceProvider extends Provider
{
    public function register()
    {
        if (function_exists('ini_set')) {
            ini_set('display_errors', 0);
        }

        $run = new Run();

        $handler = new ErrorHandler();
        $run->pushHandler($handler);

        $json_handler = new JsonErrorHandler();
        $run->pushHandler($json_handler);

        if (Misc::isCommandLine()) {
            $cli_handler = new PlainTextHandler();
            $cli_handler->addTraceFunctionArgsToOutput(true);
            $cli_handler->addTraceToOutput(true);
            $run->pushHandler($cli_handler);
        }

        $run->register();
        $this->app->instance(Run::class, $run);
    }
}
