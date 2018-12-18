<?php

namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Entity\Site\Site;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SimpleFileConfiguration extends SimpleConfiguration
{
    /**
     * @var \Concrete\Core\Entity\Site\Site
     */
    protected $site;

    /**
     * The directory where the log is to be stored.
     *
     * @var string
     */
    protected $directory;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Entity\Site\Site $site
     * @param string $directory the directory where the log is to be stored
     * @param int $coreLevel the logging level to care about for all core logs (one of the Monolog\Logger constants)
     *
     * @see \Monolog\Logger
     */
    public function __construct(Site $site, $directory, $coreLevel = Logger::DEBUG)
    {
        $this->site = $site;
        $this->directory = rtrim($directory, DIRECTORY_SEPARATOR);
        parent::__construct($coreLevel);
    }

    /**
     * Get the directory where the log is to be stored.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Get the name of the file (without path) of the log file.
     *
     * @return string
     */
    public function getFileName()
    {
        $name = $this->site->getSiteName();
        if (!$name) {
            $name = 'concrete5';
        }

        return str_replace('_', '-', trim(snake_case($name))) . '.log';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Logging\Configuration\SimpleConfiguration::createHandler()
     */
    public function createHandler($level)
    {
        $path = $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getFileName();
        $handler = new StreamHandler($path, $this->coreLevel);

        return $handler;
    }
}
