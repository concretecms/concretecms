<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Notification\Mercure\MercureService;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProcessResponseFactory
{

    /**
     * @var Token
     */
    protected $tokenService;

    /**
     * @var MercureService
     */
    protected $mercureService;

    public function __construct(MercureService $mercureService, Token $tokenService)
    {
        $this->mercureService = $mercureService;
        $this->tokenService = $tokenService;
    }

    public function createResponse(Process $process)
    {
        $requiresPolling = true;
        if ($this->mercureService->isEnabled()) {
            $requiresPolling = false;
        }
        $data = [
            'requiresPolling' => $requiresPolling,
            'process' => $process,
            'token' => $this->tokenService->generate($process->getID())
        ];

        if (!$requiresPolling) {
            // @TODO - this shouldn't be sent here, we should have this known at the dashboard level for real server-sent functionality
            $data['eventSource'] = $this->mercureService->getPublisherUrl();
        }

        return new JsonResponse($data);
    }

}
