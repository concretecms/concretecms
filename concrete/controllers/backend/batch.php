<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Command\BatchProcess;
use Concrete\Core\Command\Batch\BatchProcessorResponseFactory;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;

class Batch extends AbstractController
{

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var BatchProcessorResponseFactory
     */
    protected $responseFactory;

    public function __construct(Token $token, EntityManager $entityManager, BatchProcessorResponseFactory $responseFactory)
    {
        $this->token = $token;
        $this->entityManager = $entityManager;
        $this->responseFactory = $responseFactory;
        parent::__construct();
    }

    public function monitor($batchId, $token)
    {
        if ($this->token->validate($batchId, $token)) {
            $batchProcess = $this->entityManager->find(BatchProcess::class, $batchId);
            if ($batchProcess) {
                return $this->responseFactory->createResponse($batchProcess);
            }
        }
        throw new \Exception(t('Access Denied'));
    }

}
