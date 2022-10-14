<?php
namespace Concrete\Core\Mail;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Mail\Transport\Factory as TransportFactory;
use Laminas\Mail\Transport\TransportInterface;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $register = [
            'helper/mail' => '\Concrete\Core\Mail\Service',
            'mail' => '\Concrete\Core\Mail\Service',
        ];
        foreach ($register as $key => $value) {
            $this->app->bind($key, $value);
        }

        $this->app->bind(TransportInterface::class, function () {
            $factory = $this->app->make(TransportFactory::class);

            return $factory->createTransportFromConfig($this->app->make('config'));
        });
        $this->app->extend(
            SenderConfiguration::class,
            function (SenderConfiguration $configuration): SenderConfiguration {
                return $this->configureSenders($configuration);
            }
        );
    }

    private function configureSenders(SenderConfiguration $configuration): SenderConfiguration
    {
        return $configuration->addEntries([
            (new SenderConfiguration\Entry(tc('EmailAddress', 'Default'), 'concrete.email.default.address'))
                ->setNameKey('concrete.email.default.name')
                ->setPriority(100)
                ->setRequired(SenderConfiguration\Entry::REQUIRED_EMAIL)
            ,
            (new SenderConfiguration\Entry(t('Forgot Password'), 'concrete.email.forgot_password.address'))
                ->setNameKey('concrete.email.forgot_password.name')
            ,
            (new SenderConfiguration\Entry(t('Form Block'), 'concrete.email.form_block.address'))
                ->setNameKey('')
            ,
            (new SenderConfiguration\Entry(t('Spam Notification'), 'concrete.spam.notify_email'))
                ->setNameKey('')
            ,
            (new SenderConfiguration\Entry(t('Website Registration Notification'), 'concrete.email.register_notification.address'))
                ->setNameKey('concrete.email.register_notification.name')
            ,
            (new SenderConfiguration\Entry(t('Validate Registration'), 'concrete.email.validate_registration.address'))
                ->setNameKey('concrete.email.validate_registration.name')
            ,
            (new SenderConfiguration\Entry(t('Workflow Notification'), 'concrete.email.workflow_notification.address'))
                ->setNameKey('concrete.email.workflow_notification.name')
            ,
        ]);
    }
}
