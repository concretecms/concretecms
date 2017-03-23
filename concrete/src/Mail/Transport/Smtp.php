<?php
namespace Concrete\Core\Mail\Transport;

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
     * @param SmtpOptions $options
     * @param int $messagesPerConnection
     */
    public function __construct(SmtpOptions $options, $messagesPerConnection)
    {
        parent::__construct($options);
        $this->messagesPerConnection = $messagesPerConnection;
        $this->sentMessagesInConnection = 0;
        $this->autoDisconnect = ($this->messagesPerConnection > 1) ? false : true;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Zend\Mail\Transport\Smtp::send()
     */
    public function send(Message $message)
    {
        parent::send($message);
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
        $connection = parent::connect();
        $this->sentMessagesInConnection = 0;

        return $connection;
    }
}
