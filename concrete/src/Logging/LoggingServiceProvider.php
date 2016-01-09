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
        $this->app->bind('log', 'Concrete\Core\Logging\Logger');
    }

}
