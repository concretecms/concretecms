<?php
namespace Concrete\Core\Logging\Configuration;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Site\Service;

class ConfigurationFactory
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Service
     */
    protected $siteService;

    public function __construct(Repository $config, Service $siteService)
    {
        $this->config = $config;
        $this->siteService = $siteService;
    }

    public function createConfiguration()
    {
        $configuration = $this->config->get('concrete.log.configuration');
        if ($configuration['mode'] == 'advanced' && isset($configuration['advanced']['configuration']['loggers'])) {
            return new AdvancedConfiguration($configuration['advanced']['configuration']);
        } else {
            if (isset($configuration['simple']['handler']) && $configuration['simple']['handler'] == 'file') {
                $site = $this->siteService->getSite();
                return new SimpleFileConfiguration($site,
                    $configuration['simple']['directory'],
                    $configuration['simple']['core_logging_level']);
            } else {
                return new SimpleDatabaseConfiguration($configuration['simple']['core_logging_level']);

            }
        }

    }
}



