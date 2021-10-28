<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Messenger\MessengerConsumeResponseFactory;
use Concrete\Core\Messenger\Transport\TransportInterface;
use Concrete\Core\Messenger\Transport\TransportManager;
use Concrete\Core\Validation\CSRF\Token;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnTimeLimitListener;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\Worker;

class Messenger extends AbstractController
{

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var TransportManager
     */
    protected $transportManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RoutableMessageBus
     */
    protected $bus;

    /**
     * @var MessengerConsumeResponseFactory
     */
    protected $responseFactory;

    public function __construct(
        Token $token,
        TransportManager $transportManager,
        EventDispatcherInterface $eventDispatcher,
        RoutableMessageBus $bus,
        MessengerConsumeResponseFactory $responseFactory
    ) {
        $this->token = $token;
        $this->transportManager = $transportManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->bus = $bus;
        $this->responseFactory = $responseFactory;
        parent::__construct();
    }

    public function consume()
    {
        if ($this->token->validate('consume_messages', $this->request->request->get('token'))) {
            session_write_close();
            $logger = app(LoggerFactory::class)->createLogger(Channels::CHANNEL_MESSENGER);
            $this->eventDispatcher->addSubscriber(new StopWorkerOnMessageLimitListener(5, $logger));
            $this->eventDispatcher->addSubscriber(new StopWorkerOnTimeLimitListener(5, $logger));
            $worker = new Worker(
                [$this->transportManager->getReceivers()->get(TransportInterface::DEFAULT_ASYNC)],
                $this->bus,
                $this->eventDispatcher,
                $logger
            );
            $worker->run();
            return $this->responseFactory->createResponse();
        }
        throw new \Exception(t('Access Denied'));
    }

}
