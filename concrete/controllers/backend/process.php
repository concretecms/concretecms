<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Command\Process\ProcessResponseFactory;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Command\Process as ProcessEntity;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;

class Process extends AbstractController
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
     * @var ProcessResponseFactory
     */
    protected $responseFactory;

    public function __construct(Token $token, EntityManager $entityManager, ProcessResponseFactory $responseFactory)
    {
        $this->token = $token;
        $this->entityManager = $entityManager;
        $this->responseFactory = $responseFactory;
        parent::__construct();
    }

    public function monitor($processId, $token)
    {
        if ($this->token->validate($processId, $token)) {
            $process = $this->entityManager->find(ProcessEntity::class, $processId);
            if ($process) {
                return $this->responseFactory->createResponse($process);
            }
        }
        throw new \Exception(t('Access Denied'));
    }

}
