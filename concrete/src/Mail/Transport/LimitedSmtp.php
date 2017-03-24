<?php
namespace Concrete\Core\Mail\Transport;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\TransportInterface;

class LimitedSmtp implements TransportInterface
{
    /**
     * The actual transport instance.
     *
     * @var Smtp
     */
    protected $transport;

    /**
     * Maximum number of messages to be sent for for every connection.
     *
     * @var int
     */
    protected $limit;

    /**
     * Number of messages sent in current connection.
     *
     * @var int
     */
    protected $sent;

    /**
     * Initializes the instance.
     *
     * @param Smtp $transport the actual transport instance
     * @param int $limit The maximum number of messages to be sent per connection
     */
    public function __construct(Smtp $transport, $limit)
    {
        $this->transport = $transport;
        $this->limit = max(1, (int) $limit);
        $this->sent = 0;
    }

    /**
     * Get the actual transport instance.
     *
     * @return \Zend\Mail\Transport\Smtp
     */
    public function getSmtpTransport()
    {
        return $this->transport;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Zend\Mail\Transport\TransportInterface::send()
     */
    public function send(Message $message)
    {
        $this->transport->send($message);
        $this->trackLimit();
    }

    /**
     * Increment the counter of sent messages and disconnect the underlying transport if needed.
     */
    private function trackLimit()
    {
        ++$this->sent;
        if ($this->sent >= $this->limit) {
            $this->sent = 0;
            $this->transport->disconnect();
        }
    }
}
