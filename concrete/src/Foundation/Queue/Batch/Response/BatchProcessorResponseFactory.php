<?php

namespace Concrete\Core\Foundation\Queue\Batch\Response;

use Bernard\Queue;
use Concrete\Core\Entity\Queue\Batch;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

class BatchProcessorResponseFactory
{

    /**
     * @var Batch
     */
    protected $batch;

    /**
     * @var Token
     */
    protected $tokenService;

    public function __construct(Token $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function createResponse(Batch $batch, $moreData = [])
    {
        $data = [
            'batch' => $batch->getBatchHandle(),
            'total' => $batch->getTotal(),
            'completed' => $batch->getCompleted(),
            'token' => $this->tokenService->generate($batch->getBatchHandle())
        ];

        $data += $moreData;

        return new BatchProcessorResponse($data);
    }

    public function createEmptyResponse(string $batchHandle)
    {
        $data = [
            'batch' => $batchHandle,
            'total' => 0,
            'completed' => 0,
            'token' => $this->tokenService->generate($batchHandle)
        ];

        return new BatchProcessorResponse($data);
    }
}
