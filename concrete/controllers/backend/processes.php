<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Command\Process\ProcessConfigurationResponseFactory;
use Concrete\Core\Command\Process\ProcessResponseFactory;
use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    /**
     * @var ProcessConfigurationResponseFactory
     */
    protected $configurationResponseFactory;

    public function __construct(
        Token $token,
        EntityManager $entityManager,
        ProcessResponseFactory $responseFactory,
        ProcessConfigurationResponseFactory $configurationResponseFactory
    ) {
        $this->token = $token;
        $this->entityManager = $entityManager;
        $this->responseFactory = $responseFactory;
        $this->configurationResponseFactory = $configurationResponseFactory;
        parent::__construct();
    }

    public function getConfiguration()
    {
        if ($this->token->validate()) {
            return $this->configurationResponseFactory->createResponse();
        }
        throw new \Exception(t('Access Denied'));
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
