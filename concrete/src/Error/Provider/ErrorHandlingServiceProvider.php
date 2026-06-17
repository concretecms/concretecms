<?php
namespace Concrete\Core\Error\Provider;

use Concrete\Core\Error\Handling\ErrorHandler;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;

class ErrorHandlingServiceProvider extends Provider
{
    public function register()
    {
        if ($this->app->bound(ErrorHandler::class)) {
            $handler = $this->app->make(ErrorHandler::class);
        } else {
            $logger = $this->app->make(LoggerFactory::class)->createLogger(Channels::CHANNEL_EXCEPTIONS);
            $handler = $this->app->make(ErrorHandler::class, ['logger' => $logger]);
            $this->app->instance(ErrorHandler::class, $handler);
        }
        $handler = ErrorHandler::register($handler);
        $handler->setExceptionHandler([$handler, 'renderConcreteException']);
    }
}
