<?php

namespace Concrete\Core\Messenger\Batch;

use Concrete\Core\Entity\Messenger\BatchProcess;
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

    public function createResponse(BatchProcess $batch)
    {
        $data = [
            'batch' => $batch->getName(),
            'totalJobs' => $batch->getTotalJobs(),
            'completedJobs' => $batch->getCompletedJobs(),
            'pendingJobs' => $batch->getPendingJobs(),
            'failedJobs' => $batch->getFailedJobs(),
            'token' => $this->tokenService->generate($batch->getID())
        ];

        return new BatchProcessorResponse($data);
    }

}
