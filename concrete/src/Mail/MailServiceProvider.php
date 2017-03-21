<?php
namespace Concrete\Core\Mail;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Mail\Transport\Smtp as SmtpTransport;

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

        $this->app->singleton(SmtpTransport::class, function () use ($app) {
            return $app->build(SmtpTransport::class, ['config' => $app->make('config')->get('concrete.mail.methods.smtp')]);
        });
    }
}
