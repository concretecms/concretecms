<?php

namespace Concrete\Core\Foundation\Queue\Batch;


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


    public function __construct(DispatcherFactory $dispatcherFactory, BatchFactory $batchFactory, BatchProcessorResponseFactory $responseFactory)
    {
        $this->dispatcherFactory = $dispatcherFactory;
        $this->responseFactory = $responseFactory;
        $this->batchFactory = $batchFactory;
    }

    public function process(BatchProcessFactoryInterface $factory, array $items, $additionalResponseData = []): BatchProcessorResponse
    {
        $dispatcher = $this->dispatcherFactory->getDispatcher();
        $batch = $this->batchFactory->getBatch($factory->getBatchHandle());

        $commands = [];
        foreach($items as $item) {
            $command = $factory->getCommand($item);
            $dispatcher->dispatchOnQueue($command, $dispatcher->getQueueForCommand($command));
        }

        $this->batchFactory->incrementTotals($batch, count($items));

        return $this->responseFactory->createResponse($batch, $additionalResponseData);

    }


}