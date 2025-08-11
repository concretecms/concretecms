<?php

namespace Concrete\Core\Logging\Handler;

use Monolog\Handler\StreamHandler as MonologStreamHandler;

class StreamHandler extends MonologStreamHandler
{
    use HandlerTrait;

    /**
     * {@inheritdoc}
     *
     * @see \Monolog\Handler\StreamHandler::write()
     */
    protected function write(array $record)
    {
        if (!$this->shouldWrite($record)) {
            return;
        }
        parent::write($record);
    }
}
