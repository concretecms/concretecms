<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Command\Process\ProcessResponseFactory;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;

class Processes extends AbstractController
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
     * @var
     */
    protected $responseFactory;

    public function __construct(
        Token $token,
        EntityManager $entityManager,
        ProcessResponseFactory $responseFactory
    ) {
        $this->token = $token;
        $this->entityManager = $entityManager;
        $this->responseFactory = $responseFactory;
        parent::__construct();
    }

    public function poll()
    {
        if ($this->token->validate('poll', $this->request->request->get('token'))) {
            session_write_close();

            $processes = [];
            foreach ((array) $this->request->request->get('watchedProcessIds') as $processId) {
                $process = $this->entityManager->find(Process::class, $processId);
                if ($process) {
                    $processes[] = $process;
                }
            }

            return $this->responseFactory->createResponse($processes);
        }
        throw new \Exception(t('Access Denied'));
    }

}
