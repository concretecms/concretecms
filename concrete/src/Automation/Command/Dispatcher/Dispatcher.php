<?php

namespace Concrete\Core\Automation\Command\Dispatcher;

use Concrete\Core\Automation\Process\Response\CompletedWithFailureResponse;
use Concrete\Core\Automation\Process\Response\CompletedWithSuccessResponse;
use Concrete\Core\Automation\Process\Response\ResponseFactory;
use Concrete\Core\Automation\Process\Response\ResponseInterface;
use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Concrete\Core\Foundation\Command\SynchronousBus;

class Dispatcher
{

    /**
     * @var DispatcherFactory
     */
    protected $dispatcherFactory;

    public function __construct(
        DispatcherFactory $dispatcherFactory
    ) {
        $this->dispatcherFactory = $dispatcherFactory;
    }


    public function dispatch(CommandInterface $command): ResponseInterface
    {
        $dispatcher = $this->dispatcherFactory->getDispatcher();
        try {
            $dispatcher->dispatch($command, SynchronousBus::getHandle());
            return new CompletedWithSuccessResponse();
        } catch (\Exception $e) {
            return new CompletedWithFailureResponse($e->getMessage());
        }
    }

}
