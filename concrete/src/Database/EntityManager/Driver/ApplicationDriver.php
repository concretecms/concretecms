<?php
namespace Concrete\Core\Database\EntityManager\Driver;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Package\Package;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;

class ApplicationDriver implements DriverInterface
{

    protected $app;
    protected $config;

    public function __construct(Repository $config, Application $app)
    {
        $this->app = $app;
        $this->config = $config;
    }

    private function isLegacy()
    {
        return $this->config->get('app.enable_legacy_src_namespace');
    }

    public function getDriver()
    {
        $config = $this->config;
        if ($this->isLegacy()) {
            $appEntityPath = DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES;
            $reader = $this->app->make('orm/cachedSimpleAnnotationReader');
        } else {
            $appEntityPath = DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES . DIRECTORY_SEPARATOR . DIRNAME_ENTITIES;
            $reader = $this->app->make('orm/cachedAnnotationReader');
        }

        $appDriverSettings = $config->get(CONFIG_ORM_METADATA_APPLICATION);
        $xmlConfig = DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_XML;
        $ymlConfig = DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML;

        // Default setting so it comes first
        if (empty($appDriverSettings) && is_dir($appEntityPath)) {
            return new AnnotationDriver($reader, $appEntityPath);
        } else {
            if ($appDriverSettings === 'xml') {
                if (is_dir($xmlConfig)) {
                    return new XmlDriver($xmlConfig);
                } else {
                    // Fallback to default
                    return new AnnotationDriver($reader, $appEntityPath);
                }
            } else {
                if ($appDriverSettings === 'yaml' || $appDriverSettings === 'yml') {
                    if (is_dir($ymlConfig)) {
                        return new YamlDriver($ymlConfig);
                    } else {
                        // Fallback to default
                        return new AnnotationDriver($reader, $appEntityPath);
                    }
                }
            }
        }
    }

    public function getNamespace()
    {
        if ($this->isLegacy()) {
            return 'Application\Src';
        } else {
            return 'Application\Entity';
        }
    }


}

