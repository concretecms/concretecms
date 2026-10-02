<?php

namespace Concrete\Core\Logging\Configuration;

interface ConfigurationInterface
{
    /**
     * Create a new logger instance for a specific logigng channel.
     *
     * @param string $channel The name of logging channel
     *
     * @return \Monolog\Logger
     */
    public function createLogger($channel);
}
