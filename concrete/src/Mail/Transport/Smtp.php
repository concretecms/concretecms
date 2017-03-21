<?php
namespace Concrete\Core\Mail\Transport;

use Exception;
use Throwable;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as ZendSmtp;
use Zend\Mail\Transport\SmtpOptions;

class Smtp extends ZendSmtp
{
    /**
     * Maximum number of messages to be sent for for every connection.
     *
     * @var int
     */
    protected $messagesPerConnection;

    /**
     * Number of messages sent in current connection.
     *
     * @var int
     */
    protected $sentMessagesInConnection = 0;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $options = [
            'host' => $config['server'],
        ];
        $username = isset($config['username']) ? (string) $config['username'] : '';
        if ($username !== '') {
            $options['connection_class'] = 'login';
            $options['connection_config'] = [
                'username' => $username,
                'password' => isset($config['password']) ? (string) $config['password'] : '',
            ];
        }
        if (isset($config['port']) && $config['port']) {
            $options['port'] = (int) $config['port'];
        }
        if (isset($config['encryption']) && $config['encryption']) {
            $options['connection_config']['ssl'] = $config['encryption'];
        }
        if (isset($config['messages_per_connection']) && $config['messages_per_connection']) {
            $this->messagesPerConnection = max(1, (int) $config['messages_per_connection']);
        } else {
            $this->messagesPerConnection = 1;
        }
        parent::__construct(new SmtpOptions($options));
        $this->autoDisconnect = ($this->messagesPerConnection > 1) ? false : true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Zend\Mail\Transport\Smtp::send()
     */
    public function send(Message $message)
    {
        $error = null;
        try {
            parent::send($message);
        } catch (Exception $x) {
            $error = $x;
        } catch (Throwable $x) {
            $error = $x;
        }
        if ($error !== null) {
            try {
                $this->disconnect();
            } catch (Exception $foo) {
            }
            throw $error;
        }
        ++$this->sentMessagesInConnection;
        if ($this->sentMessagesInConnection >= $this->messagesPerConnection) {
            $this->disconnect();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Zend\Mail\Transport\Smtp::disconnect()
     */
    protected function connect()
    {
        parent::connect();
        $this->sentMessagesInConnection = 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Zend\Mail\Transport\Smtp::disconnect()
     */
    public function disconnect()
    {
        parent::disconnect();
        $this->sentMessagesInConnection = 0;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Zend\Mail\Transport\Smtp::__destruct()
     */
    public function __destruct()
    {
        $this->disconnect();
        parent::__destruct();
    }
}
