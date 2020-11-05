<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Foundation\Queue\Batch\BatchFactory;
use Concrete\Core\Foundation\Queue\Batch\Response\BatchProcessorResponseFactory;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Foundation\Queue\Response\QueueProgressResponse;
use Concrete\Core\Validation\CSRF\Token;

class Batch extends AbstractController
{

    /**
     * @var QueueService
     */
    protected $service;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var BatchFactory
     */
    protected $batchFactory;

    /**
     * @var BatchProcessorResponseFactory
     */
    protected $batchProcessorResponseFactory;

    public function __construct(QueueService $service, Token $token, BatchFactory $batchFactory, BatchProcessorResponseFactory $batchProcessorResponseFactory)
    {
        $this->service = $service;
        $this->token = $token;
        $this->batchFactory = $batchFactory;
        $this->batchProcessorResponseFactory = $batchProcessorResponseFactory;
        parent::__construct();
    }

    public function monitor($handle, $token)
    {
        if ($this->token->validate($handle, $token)) {
            $batch = $this->batchFactory->getBatch($handle);
            if ($batch) {
                $this->service->consumeBatchFromPoll($batch);
                return $this->batchProcessorResponseFactory->createResponse($batch);
            } else {
                return $this->batchProcessorResponseFactory->createEmptyResponse($handle);
            }
        }
        throw new \Exception(t('Access Denied'));
    }

}
