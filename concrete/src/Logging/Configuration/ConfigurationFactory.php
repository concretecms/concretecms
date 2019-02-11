<?php
namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;

class ConfigurationFactory
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * The IOC container we use to build configurations
     *
     * @var Application
     */
    protected $app;

    public function __construct(Repository $config, Application $app)
    {
        $this->config = $config;
        $this->app = $app;
    }

    public function createConfiguration()
    {
        $configuration = $this->config->get('concrete.log.configuration');
        if ($configuration['mode'] == 'advanced' && isset($configuration['advanced']['configuration']['loggers'])) {
            return new AdvancedConfiguration($configuration['advanced']['configuration']);
        } else {
            if (isset($configuration['simple']['handler']) && $configuration['simple']['handler'] == 'file') {
                return $this->app->make(SimpleFileConfiguration::class, [
                    'filename' => array_get($configuration, 'simple.file.file'),
                    'coreLevel' => array_get($configuration, 'simple.core_logging_level')
                ]);
            } else {
                return $this->app->make(SimpleDatabaseConfiguration::class, [
                    'coreLevel' => array_get($configuration, 'simple.core_logging_level')
                ]);
            }
        }

    }
}



