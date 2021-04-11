<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProcessResponseFactory
{

    /**
     * @var Token
     */
    protected $tokenService;

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config, Token $tokenService)
    {
        $this->config = $config;
        $this->tokenService = $tokenService;
    }

    /**
     * @param Process|Process[] $process
     * @return array
     */
    public function getData($process)
    {
        $data = ['viewToken' => $this->tokenService->generate('view_activity')];
        if (is_array($process)) {
            $data['processes'] = [];
            foreach($process as $individualProcess) {
                $data['processes'][] = $individualProcess;
            }
        } else {
            $data['process'] = $process;
        }

        $consumeMethod = $this->config->get('concrete.messenger.consume.method');
        if ($consumeMethod === 'app') { // this is the default. we consume through the UI
            $data['consumeToken'] = $this->tokenService->generate('consume_messages');
        }

        return $data;
    }


    /**
     * @param Process|Process[] $process
     * @return JsonResponse
     */
    public function createResponse($process)
    {
        return new JsonResponse($this->getData($process));
    }

}
