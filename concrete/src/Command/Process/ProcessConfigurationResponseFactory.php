<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Notification\Events\MercureService;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProcessConfigurationResponseFactory
{

    /**
     * @var Token
     */
    protected $tokenService;

    /**
     * @var MercureService
     */
    protected $mercureService;

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config, MercureService $mercureService, Token $tokenService)
    {
        $this->config = $config;
        $this->mercureService = $mercureService;
        $this->tokenService = $tokenService;
    }

    /**
     * @return JsonResponse
     */
    public function createResponse()
    {
        $requirePolling = false;
        if (!$this->mercureService->isEnabled()) {
            $requirePolling = true;
        }
        $data = [
            'requiresPolling' => $requirePolling,
            'pollToken' => $this->tokenService->generate('poll'),
        ];

        return new JsonResponse($data);
    }

}
