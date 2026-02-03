<?php

namespace Concrete\Core\Logging\Processor;

use Concrete\Core\Config\Repository\Repository;
use Throwable;

/**
 * A processor for adding the exception stack traces to the logged errors
 */
class StackTraceProcessor
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Invoke this processor
     *
     * @param array $record The given monolog record
     *
     * @return array The modified record
     */
    public function __invoke(array $record): array
    {
        $throwable = $record['context']['exception'] ?? null;
        if ($throwable instanceof Throwable && $this->isLoggingStackTrace()) {
            $message = rtrim((string) ($record['message'] ?? ''));
            if ($message !== '') {
                $message .= "\n";
            }
            $record['message'] = $message . t('Stack Trace: %s', $throwable->getTraceAsString());
        }

        return $record;
    }

    /**
     * Should we log the stack trace of uncaught exceptions?
     */
    protected function isLoggingStackTrace(): bool
    {
        return (bool) $this->config->get('concrete.log.stack_trace');
    }
}
