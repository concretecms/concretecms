<?php
namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Config\Repository\Repository;

class ConfigurationFactory
{

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function createConfiguration()
    {
        $configuration = $this->config->get('concrete.log.configuration');
        if ($configuration['mode'] == 'advanced') {
            return new AdvancedConfiguration($configuration['advanced']['configuration']);
        }

        return new SimpleConfiguration($configuration['simple']['core_logging_level']);
    }
}



