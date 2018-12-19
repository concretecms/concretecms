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
        if ($configuration['mode'] == 'advanced' && isset($configuration['advanced']['configuration']['loggers'])) {
            return new AdvancedConfiguration($configuration['advanced']['configuration']);
        } else {
            if (isset($configuration['simple']['handler']) && $configuration['simple']['handler'] == 'file') {
                return new SimpleFileConfiguration(
                    $configuration['simple']['file']['file'],
                    $configuration['simple']['core_logging_level']
                );
            } else {
                return new SimpleDatabaseConfiguration($configuration['simple']['core_logging_level']);

            }
        }

    }
}



