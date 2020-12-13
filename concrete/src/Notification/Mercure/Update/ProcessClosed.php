<?php

namespace Concrete\Core\Notification\Mercure\Update;

use Concrete\Core\Entity\Command\Process;

class ProcessClosed implements UpdateInterface
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

    public function getTopicURL(): string
    {
        return 'https://global.concretecms.com/task/close-process/' . $this->processData['id'];
    }

    public function getData(): array
    {
        return [
            'process' => $this->processData,
            'exitCode' => $this->exitCode,
        ];
    }

}

