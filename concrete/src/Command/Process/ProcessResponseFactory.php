<?php

namespace Concrete\Core\Command\Batch;

use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Validation\CSRF\Token;

class ProcessResponseFactory
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

    public function createResponse(Process $process)
    {
        $data = [
            'id' => $process->getID(),
            'name' => $process->getName(),
            'totalJobs' => $batch->getTotalJobs(),
            'completedJobs' => $batch->getCompletedJobs(),
            'pendingJobs' => $batch->getPendingJobs(),
            'failedJobs' => $batch->getFailedJobs(),
            'token' => $this->tokenService->generate($batch->getID())
        ];

        return new BatchProcessorResponse($data);
    }

}
