<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Notification\Events\Topic\ConcreteProcessTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;

class ProcessClosedEvent extends AbstractConcreteEvent
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

    protected static function getEvent(): string
    {
        return 'CloseProcess';
    }

    protected static function createTopic(string $slug): TopicInterface
    {
        return new ConcreteProcessTopic($slug);
    }

    protected function getEventData(): array
    {
        return [
            'process' => $this->processData,
            'exitCode' => $this->exitCode,
        ];
    }

}

