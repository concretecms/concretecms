<?php
namespace Concrete\Core\Mail\Transport;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

class Factory implements FactoryInterface, ApplicationAwareInterface, LoggerAwareInterface
{
    use ApplicationAwareTrait;
    use LoggerAwareTrait;

    /**
     * Create a Transport instance from a configuration repository.
     *
     * @param Repository $config
     *
     * @return TransportInterface
     * @deprecated
     */
    public function createTransportFromConfig(Repository $config)
    {
        return $this->createTransportFromArray($config->get('concrete.mail'));
    }

    /**
     * Create a Transport instance from a configuration array.
     *
     * @param array $array
     *
     * @return TransportInterface
     */
    public function createTransportFromArray(array $array)
    {
        switch (array_get($array, 'method')) {
            case 'smtp':
                return $this->createSmtpTransportFromArray(array_get($array, 'methods.smtp'));
            case 'PHP_MAIL':
            default:
                return $this->createPhpMailTransportFromArray(array_get($array, 'methods.php_mail') ?: []);
        }
    }

    /**
     * @param array $array
     * @return SendmailTransport
     */
    public function createPhpMailTransportFromArray(array $array)
    {
        return new SendmailTransport(
            ($array['parameters'] ?? '') ?: null,
            $this->getDispatcher(),
            $this->logger
        );
    }

    /**
     * @param array $array
     * @return EsmtpTransport
     */
    public function createSmtpTransportFromArray(array $array)
    {
        return (new EsmtpTransport(
            (string) ($array['server'] ?? ''),
            (int) ($array['port'] ?? 0),
            (string) ($array['encryption'] ?? ''),
            $this->getDispatcher(),
            $this->logger,
        ))
            ->setRestartThreshold(max(0, (int) ($array['messages_per_connection'] ?? 0)))
            ->setUsername((string) ($array['username'] ?? ''))
            ->setPassword((string) ($array['password'] ?? ''))
            ->setLocalDomain((string) ($array['helo_domain'] ?? ''));
    }

    protected function getDispatcher(): ?EventDispatcher
    {
        try {
            return $this->app->make('director')->getEventDispatcher();
        } catch (BindingResolutionException $e) {
            return null;
        }
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_EMAIL;
    }
}
