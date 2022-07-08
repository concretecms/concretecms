<?php
namespace Concrete\Core\Messenger\Transport\Sender;

use Concrete\Core\Messenger\Transport\TransportManager;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocatorInterface;

class DefinedTransportSendersLocator implements SendersLocatorInterface
{

    /**
     * @var string
     */
    protected $transportHandle;

    /**
     * @var TransportManager
     */
    protected $transportManager;

    /**
     * DefinedTransportSendersLocator constructor.
     * @param string $transportHandle
     */
    public function __construct(string $transportHandle, TransportManager $transportManager)
    {
        $this->transportHandle = $transportHandle;
        $this->transportManager = $transportManager;
    }

    public function getSenders(Envelope $envelope): iterable
    {
        $sendersLocator = $this->transportManager->getSenders();
        yield $this->transportHandle => $sendersLocator->get($this->transportHandle);
    }


}