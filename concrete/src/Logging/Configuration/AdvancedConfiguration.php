<?php

namespace Concrete\Core\Logging\Configuration;

use Cascade\Cascade;
use Concrete\Core\Logging\Channels;
use Monolog\Processor\PsrLogMessageProcessor;

class AdvancedConfiguration implements ConfigurationInterface
{
    protected $config = [];

    public function __construct($config)
    {
        $config = $this->prepareConfig($config);
        $this->config = $config;
    }

    /**
     * Takes the config array we're going to pass to monolog cascade and transforms it a bit. For example, if we have
     * configuration we need to apply to all channels, we loop through all the channels in the channels class and
     * add them to the config array.
     * @param array $config
     */
    protected function prepareConfig($config)
    {
        if (isset($config['loggers']) && is_array($config['loggers'])
            && array_key_exists(Channels::META_CHANNEL_ALL, $config['loggers'])) {
    
            $allConfig = $config['loggers'][Channels::META_CHANNEL_ALL];
            $channels = array_merge(Channels::getCoreChannels(), [Channels::CHANNEL_APPLICATION]);
            foreach($channels as $channel) {
                $config['loggers'][$channel] = $allConfig;
            }
            unset($config['loggers'][Channels::META_CHANNEL_ALL]);
        }
        return $config;
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
        $logger->pushProcessor(new PsrLogMessageProcessor());
        return $logger;
    }
}
