<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class LoggingServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the logger singleton
        $this->app->singleton('Concrete\Core\Logging\Logger', function ($app) {
            // @todo support log level
            return $app->build('Concrete\Core\Logging\Logger', array(Logger::CHANNEL_APPLICATION));
        });

        // Bind the PSR-3 logger interface against the singleton
        $this->app->bind('Psr\Log\LoggerInterface', 'Concrete\Core\Logging\Logger');
        $this->app->singleton('log', function() {
            $logger = new Logger(Logger::CHANNEL_APPLICATION);
            return $logger;
        });
        $this->app->singleton('log/exceptions', function() {
            $logger = new Logger(LOG_TYPE_EXCEPTIONS);
            return $logger;
        });
    }
}
