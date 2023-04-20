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
        $fallbackToDefaultNotes = t("If not specified, we'll use the email address specified in the %s section.", '<code>' . tc('EmailAddress', 'Default') . '</code>');
        return $configuration->addEntries([
            (new SenderConfiguration\Entry(tc('EmailAddress', 'Default'), 'concrete.email.default.address'))
                ->setNameKey('concrete.email.default.name')
                ->setPriority(100)
                ->setRequired(SenderConfiguration\Entry::REQUIRED_EMAIL)
            ,
            (new SenderConfiguration\Entry(t('Forgot Password'), 'concrete.email.forgot_password.address'))
                ->setNameKey('concrete.email.forgot_password.name')
                ->setNotes($fallbackToDefaultNotes)
            ,
            (new SenderConfiguration\Entry(t('Form Block'), 'concrete.email.form_block.address'))
                ->setNameKey('')
                ->setNotes($fallbackToDefaultNotes)
            ,
            (new SenderConfiguration\Entry(t('Spam Notification'), 'concrete.spam.notify_email'))
                ->setNameKey('')
                ->setNotes(t("If not specified, spam notifications won't be sent."))
            ,
            (new SenderConfiguration\Entry(t('Website Registration Notification'), 'concrete.email.register_notification.address'))
                ->setNameKey('concrete.email.register_notification.name')
                ->setNotes($fallbackToDefaultNotes)
            ,
            (new SenderConfiguration\Entry(t('Validate Registration'), 'concrete.email.validate_registration.address'))
                ->setNameKey('concrete.email.validate_registration.name')
                ->setNotes($fallbackToDefaultNotes)
            ,
            (new SenderConfiguration\Entry(t('Workflow Notification'), 'concrete.email.workflow_notification.address'))
                ->setNameKey('concrete.email.workflow_notification.name')
                ->setNotes($fallbackToDefaultNotes)
            ,
        ]);
    }
}
