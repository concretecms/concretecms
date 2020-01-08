<?php

namespace Concrete\Core\Foundation\Queue\Batch;


use Concrete\Core\Foundation\Command\AsynchronousBus;
use Concrete\Core\Foundation\Command\Dispatcher;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Concrete\Core\Foundation\Queue\Batch\Response\BatchProcessorResponse;
use Concrete\Core\Foundation\Queue\Batch\Response\BatchProcessorResponseFactory;

class Processor implements ProcessorInterface
{

    /**
     * @var BatchFactory
     */
    protected $batchFactory;


    /**
     * @var BatchProcessorResponseFactory
     */
    protected $responseFactory;


    /**
     * @var DispatcherFactory
     */
    protected $dispatcherFactory;

    /**
     * @var BatchProgressUpdater
     */
    protected $updater;


    public function __construct(DispatcherFactory $dispatcherFactory, BatchFactory $batchFactory, BatchProgressUpdater $updater, BatchProcessorResponseFactory $responseFactory)
    {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->updater = $updater;
        $this->responseFactory = $responseFactory;
        $this->batchFactory = $batchFactory;
    }

    public function process(BatchProcessFactoryInterface $factory, $mixed, $additionalResponseData = []): BatchProcessorResponse
    {
        $dispatcher = $this->dispatcherFactory->getDispatcher();
        $batch = $this->batchFactory->createOrGetBatch($factory->getBatchHandle());
        $commands = $factory->getCommands($mixed);

        foreach($commands as $command) {
            $dispatcher->dispatch($command, AsynchronousBus::getHandle());
        }

        $this->updater->incrementTotals($batch, count($commands));

        return $this->responseFactory->createResponse($batch, $additionalResponseData);

    }


}