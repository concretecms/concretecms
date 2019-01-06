<?php

namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Logging\Handler\DatabaseHandler;
use Monolog\Formatter\LineFormatter;

class SimpleDatabaseConfiguration extends SimpleConfiguration
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\Configuration\SimpleConfiguration::createHandler()
     */
    protected function createHandler($level)
    {
        $handler = new DatabaseHandler($level);
        // set a more basic formatter.
        $output = '%message%';
        $formatter = new LineFormatter($output, null, true);
        $handler->setFormatter($formatter);

        return $handler;
    }
}
