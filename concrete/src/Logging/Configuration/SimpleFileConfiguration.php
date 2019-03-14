<?php

namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Entity\Site\Site;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SimpleFileConfiguration extends SimpleConfiguration
{

    /**
     * The file where the log is to be stored.
     *
     * @var string
     */
    protected $filename;

    /**
     * Initialize the instance.
     *
     * @param string $filename the file to log to
     * @param int $coreLevel the logging level to care about for all core logs (one of the Monolog\Logger constants)
     *
     * @see \Monolog\Logger
     */
    public function __construct($filename, $coreLevel = Logger::DEBUG)
    {
        $this->filename = $filename;
        parent::__construct($coreLevel);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\Configuration\SimpleConfiguration::createHandler()
     */
    public function createHandler($level)
    {
        $handler = new StreamHandler($this->filename, $this->coreLevel);
        return $handler;
    }
}
