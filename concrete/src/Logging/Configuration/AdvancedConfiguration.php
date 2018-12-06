<?php
namespace Concrete\Core\Logging\Configuration;

use Cascade\Cascade;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Handler\DatabaseHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

class AdvancedConfiguration implements ConfigurationInterface
{

    protected $config = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function createLogger($channel)
    {
        Cascade::loadConfigFromArray($this->config);
        $logger = Cascade::getLogger($channel);
        return $logger;
    }


}



