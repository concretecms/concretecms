<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

class ProcessClosed implements EventInterface
{

    /**
     * @var array
     */
    protected $processData;

    /**
     * @var integer
     */
    protected $exitCode;

    /**
     * ProcessClosed constructor.
     * @param array $processData
     * @param int $exitCode
     */
    public function __construct(array $processData, int $exitCode)
    {
        $this->processData = $processData;
        $this->exitCode = $exitCode;
    }

    public function getEvent(): string
    {
        return 'CloseProcess';
    }

    public function getData(): array
    {
        return [
            'process' => $this->processData,
            'exitCode' => $this->exitCode,
        ];
    }

}

