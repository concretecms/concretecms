<?php

namespace Concrete\Core\Command\Task\Response;

use Concrete\Core\Command\Process\ProcessResponseFactory;
use Concrete\Core\Command\Task\Runner\Response\ProcessStartedResponse;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface as RunnerResponseInterface;
use Concrete\Core\Command\Task\Runner\Response\TaskCompletedResponse;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class HttpResponseFactory implements ResponseFactoryInterface
{
    const STATUS_COMPLETED = 'completed';
    const STATUS_PROCESS_STARTED = 'process_started';

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ProcessResponseFactory
     */
    protected $responseFactory;

    public function __construct(Session $session, ProcessResponseFactory $responseFactory)
    {
        $this->session = $session;
        $this->responseFactory = $responseFactory;
    }

    public function createResponse(RunnerResponseInterface $response)
    {
        if ($response instanceof TaskCompletedResponse) {
            $this->session->getFlashBag()->add('page_message', ['success', $response->getMessage()]);
            return new JsonResponse(['status' => self::STATUS_COMPLETED]);
        }
        if ($response instanceof ProcessStartedResponse) {
            if (!$response->getProcess()->getBatch()) {
                // we show a message if you're not starting a batch process.
                $this->session->getFlashBag()->add('page_message', ['message', $response->getMessage()]);
            }
            return new JsonResponse(
                [
                    'response' => $this->responseFactory->getData($response->getProcess()),
                    'status' => self::STATUS_PROCESS_STARTED
                ]
            );
        }
    }
}
