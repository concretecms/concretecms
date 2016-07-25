<?php
namespace Concrete\Core\Service;

use Concrete\Core\Service\Configuration\GeneratorInterface;
use Concrete\Core\Service\Configuration\StorageInterface;
use Concrete\Core\Service\Configuration\ConfiguratorInterface;

interface ServiceInterface
{
    /**
     * Get the human readable service name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the version.
     *
     * @return string
     */
    public function getVersion();

    /**
     * Get the human readable service name with the version.
     *
     * @return string
     */
    public function getFullName();

    /**
     * Get a detector instance to determine if this service is active.
     *
     * @return \Concrete\Core\Service\Detector\DetectorInterface
     */
    public function getDetector();

    /**
     * Get the configuration generator instance.
     *
     * @return GeneratorInterface
     */
    public function getGenerator();

    /**
     * Get the storage reader/writer instance.
     *
     * @return StorageInterface
     */
    public function getStorage();

    /**
     * Get the configurator instance.
     *
     * @return ConfiguratorInterface
     */
    public function getConfigurator();
}
