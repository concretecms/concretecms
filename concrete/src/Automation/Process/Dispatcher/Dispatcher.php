<?php

namespace Concrete\Core\Automation\Process\Dispatcher;

use Concrete\Core\Automation\Command\CommandFactory;
use Concrete\Core\Automation\Process\Dispatcher\Response\HttpResponseFactory;
use Concrete\Core\Automation\Process\Dispatcher\Response\ResponseInterface;
use Concrete\Core\Automation\Process\Response\CompletedResponseInterface;
use Concrete\Core\Entity\Automation\Process;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Automation\Command\Dispatcher\Dispatcher as CommandDispatcher;

class Dispatcher
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var CommandFactory
     */
    protected $commandFactory;

    /**
     * @var CommandDispatcher
     */
    protected $commandDispatcher;

    /**
     * @var HttpResponseFactory
     */
    protected $responseFactory;

    public function __construct(
        EntityManager $entityManager,
        CommandFactory $commandFactory,
        CommandDispatcher $commandDispatcher,
        HttpResponseFactory $responseFactory
    ) {
        $this->entityManager = $entityManager;
        $this->commandFactory = $commandFactory;
        $this->commandDispatcher = $commandDispatcher;
        $this->responseFactory = $responseFactory;
    }

    public function dispatch(Process $process): ResponseInterface
    {
        $this->entityManager->persist($process);
        $this->entityManager->flush();

        $command = $this->commandFactory->createCommand($process);
        $response = $this->commandDispatcher->dispatch($command);

        if ($response instanceof CompletedResponseInterface) {
            $process->setCompleted(time());
            $this->entityManager->persist($process);
            $this->entityManager->flush();
        }

        return $this->responseFactory->createResponse($response);
    }

}
