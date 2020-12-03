<?php

namespace Concrete\Core\Notification\Mercure\Update;

use Concrete\Core\Entity\Command\Process;

class ProcessOutput implements UpdateInterface
{

    /**
     * @var string
     */
    protected $processId;

    /**
     * @var string
     */
    protected $message;

    public function __construct(string $processId, string $message)
    {
        $this->processId = $processId;
        $this->message = $message;
    }

    public function getTopicURL(): string
    {
        return 'https://global.concretecms.com/task/processes/' . $this->processId;
    }

    public function getData(): array
    {
        return ['message' => $this->message];
    }

}

