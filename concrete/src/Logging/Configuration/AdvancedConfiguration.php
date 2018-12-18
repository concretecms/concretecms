<?php

namespace Concrete\Core\Logging\Configuration;

use Cascade\Cascade;

class AdvancedConfiguration implements ConfigurationInterface
{
    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @see \Concrete\Core\Logging\Configuration\ConfigurationInterface::createLogger()
     */
    public function createLogger($channel)
    {
        Cascade::loadConfigFromArray($this->config);
        $logger = Cascade::getLogger($channel);

        return $logger;
    }
}
