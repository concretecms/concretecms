<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Config\Repository\Repository;
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

    public function createResponse(Process $process)
    {
        $data = [
            'process' => $process,
            'viewToken' => $this->tokenService->generate('view_activity')
        ];

        $consumeMethod = $this->config->get('concrete.messenger.consume.method');
        if ($consumeMethod === 'app') { // this is the default. we consume through the UI
            $data['consumeToken'] = $this->tokenService->generate('consume_messages');
        }

        return new JsonResponse($data);
    }

}
