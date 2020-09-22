<?php

namespace Concrete\Core\Automation\Task\Response;

use Concrete\Core\Automation\Task\Runner\Response\ProcessStartedResponse;
use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface as RunnerResponseInterface;
use Concrete\Core\Automation\Task\Runner\Response\TaskCompletedResponse;
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

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function createResponse(RunnerResponseInterface $response)
    {
        if ($response instanceof TaskCompletedResponse) {
            $this->session->getFlashBag()->add('page_message', ['success', $response->getMessage()]);
            return new JsonResponse(['status' => self::STATUS_COMPLETED]);
        }
        if ($response instanceof ProcessStartedResponse) {
            $this->session->getFlashBag()->add('page_message', ['message', $response->getMessage()]);
            return new JsonResponse(
                [
                    'processId' => $response->getProcess()->getID(),
                    'status' => self::STATUS_PROCESS_STARTED
                ]
            );
        }
    }
}
