<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProcessResponseFactory
{

    /**
     * @var Token
     */
    protected $tokenService;

    public function __construct(Token $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function createResponse(Process $process)
    {
        $data = [
            'process' => $process,
            'token' => $this->tokenService->generate($process->getID())
        ];

        return new JsonResponse($data);
    }

}
