<?php
namespace Concrete\Core\Mail;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Mail\Transport\Factory as TransportFactory;
use Zend\Mail\Transport\TransportInterface;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $app = $this->app;

        $register = [
            'helper/mail' => '\Concrete\Core\Mail\Service',
            'mail' => '\Concrete\Core\Mail\Service',
        ];

        foreach ($register as $key => $value) {
            $app->bind($key, $value);
        }

        $this->app->singleton(TransportInterface::class, function () use ($app) {
            $factory = $app->make(TransportFactory::class);

            return $factory->createTransportFromConfig($app->make('config'));
        });
    }
}
