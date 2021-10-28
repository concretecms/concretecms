<?php
namespace Concrete\Core\Messenger\Transport;

use Concrete\Core\Messenger\Transport\Receiver\ReceiverLocator;
use Concrete\Core\Messenger\Transport\Sender\SenderLocator;

class TransportManager
{

    /**
     * @var SenderLocator
     */
    protected $senders;

    /**
     * @var ReceiverLocator
     */
    protected $receivers;

    public function __construct()
    {
        $this->senders = new SenderLocator();
        $this->receivers = new ReceiverLocator();
    }

    public function addTransport(TransportInterface $transport)
    {
        foreach($transport->getSenders() as $id => $sender) {
            $this->senders->addSender($id, $sender);
        }
        foreach($transport->getReceivers() as $id => $receiver) {
            $this->receivers->addReceiver($id, $receiver);
        }
    }

    /**
     * @return SenderLocator
     */
    public function getSenders(): SenderLocator
    {
        return $this->senders;
    }

    /**
     * @return ReceiverLocator
     */
    public function getReceivers(): ReceiverLocator
    {
        return $this->receivers;
    }



}