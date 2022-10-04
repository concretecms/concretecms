<?php
namespace Concrete\Core\Error\Provider;

use Concrete\Core\Error\Handler\ErrorHandler;
use Concrete\Core\Error\Handler\JsonErrorHandler;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Error\Handler\PlainTextHandler;
use Whoops\Run;
use Whoops\Util\Misc;

class WhoopsServiceProvider extends Provider
{
    public function register()
    {
        if (function_exists('ini_set')) {
           // ini_set('display_errors', 0);
        }

        $run = new Run();

        $handler = new ErrorHandler();

        // Disallow all ENV, SERVER, and COOKIE keys
        $disallow = $this->getDisallowedKeys();

        foreach ($disallow as $global => $keys) {
            foreach ($keys as $key) {
                $handler->hideSuperglobalKey($global, $key);
            }
        }

        $run->pushHandler($handler);

        $json_handler = new JsonErrorHandler();
        $run->pushHandler($json_handler);

        if (Misc::isCommandLine()) {
            $cli_handler = new PlainTextHandler();
            if (method_exists($cli_handler, 'setDumper')) {
                // Available since Whoops 2.1.10
                $cli_handler->setDumper(function ($var) {
                    var_dump_safe($var, true, 2);
                });
            }
            $cli_handler->addTraceFunctionArgsToOutput(true);
            $cli_handler->addTraceToOutput(true);
            $run->pushHandler($cli_handler);
        }

        $run->register();
        $this->app->instance(Run::class, $run);
    }

    /**
     * Get the list of superglobal keys that should be masked in whoops output
     *
     * @return array<string, string[]> A list of disallowed superglobal keys [`_SERVER' => ['some_key']]
     */
    protected function getDisallowedKeys(): array
    {
        return [
            '_ENV' => array_keys($_ENV),
            '_SERVER' => array_keys($_SERVER),
            '_COOKIE' => array_keys($_COOKIE),
        ];
    }
}
