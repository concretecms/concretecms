<?php
namespace Concrete\Core\Mail\Transport;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Mail\Transport\LimitedSmtp as LimitedSmtpTransport;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class Factory
{
    /**
     * * Create a Transport instance from a configuration repository.
     *
     * @param Repository $config
     *
     * @return \Zend\Mail\Transport\TransportInterface
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
     * @return \Zend\Mail\Transport\TransportInterface
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
     *
     * @return \Zend\Mail\Transport\Sendmail
     */
    public function createPhpMailTransportFromArray(array $array)
    {
        $parameters = isset($array['parameters']) ? $array['parameters'] : '';

        return new SendmailTransport($parameters);
    }

    /**
     * @param array $array
     *
     * @return \Concrete\Core\Mail\Transport\Smtp|\Concrete\Core\Mail\Transport\LimitedSmtp
     */
    public function createSmtpTransportFromArray(array $array)
    {
        $options = [
            'host' => (string) array_get($array, 'server'),
        ];
        $username = (string) array_get($array, 'username', '');
        if ($username !== '') {
            $options['connection_class'] = 'login';
            $options['connection_config'] = [
                'username' => $username,
                'password' => (string) array_get($array, 'password'),
            ];
        }
        $port = array_get($array, 'port');
        if ($port) {
            $options['port'] = (int) $array['port'];
        }
        $encryption = array_get($array, 'encryption');
        if ($encryption) {
            $options['connection_config']['ssl'] = (string) $encryption;
        }
        $mpc = array_get($array, 'messages_per_connection');
        $messagesPerConnection = $mpc ? (int) $mpc : 0;

        $smtp = new SmtpTransport(new SmtpOptions($options));

        if ($messagesPerConnection >= 1) {
            $result = new LimitedSmtpTransport($smtp, $messagesPerConnection);
        } else {
            $result = $smtp;
        }

        return $result;
    }
}
