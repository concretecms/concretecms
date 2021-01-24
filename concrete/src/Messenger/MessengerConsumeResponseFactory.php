<?php
namespace Concrete\Core\Messenger;

use Concrete\Core\Entity\Command\Process as ProcessEntity;
use Concrete\Core\Messenger\Transport\TransportInterface;
use Concrete\Core\Messenger\Transport\TransportManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;

/**
 * This class is used to send responses when using app-powered message consumption. This is the default because it
 * relies on zero configuration but you really should use the CLI messenger:consume command instead.
 *
 * Class MessengerConsumeResponseFactory
 * @package Concrete\Core\Messenger
 */
class MessengerConsumeResponseFactory
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var TransportManager
     */
    protected $transportManager;

    /**
     * MessengerConsumeResponseFactory constructor.
     * @param EntityManager $entityManager
     * @param TransportManager $transportManager
     */
    public function __construct(EntityManager $entityManager, TransportManager $transportManager)
    {
        $this->entityManager = $entityManager;
        $this->transportManager = $transportManager;
    }

    /**
     * @param string[] $watchedProcessIds
     * @return JsonResponse
     */
    public function createResponse()
    {
        $messages = -1;
        $transport = $this->transportManager->getReceivers()->get(TransportInterface::DEFAULT_ASYNC);
        if ($transport instanceof MessageCountAwareInterface && $transport->getMessageCount() > 0) {
            $messages = $transport->getMessageCount();
        }
        return new JsonResponse(['messages' => $messages]);
    }
}