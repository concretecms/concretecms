<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

class ProcessOutput implements EventInterface
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

    public function getEvent(): string
    {
        return 'ProcessOutput';
    }

    public function getData(): array
    {
        return [
            'processId' => $this->processId,
            'message' => $this->message
        ];
    }

}

