<?php
namespace Concrete\Core\Service\Manager;

use Concrete\Core\Application\Application;
use Concrete\Core\Service\ServiceInterface;

class ServiceManager implements ManagerInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $extensions = array();

    /**
     * @var ServiceInterface[]
     */
    protected $services = array();

    /**
     * Manager constructor.
     *
     * @param \Concrete\Core\Application\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Add an extension to this manager.
     *
     * @param string $handle
     * @param string|callable|ServiceInterface $abstract
     */
    public function extend($handle, $abstract)
    {
        $this->extensions[$handle] = $abstract;
    }

    /**
     * An array of handles that have been added with `->extend`.
     *
     * @return string[]
     */
    public function getExtensions()
    {
        return array_keys($this->extensions);
    }

    /**
     * Does this handle exist?
     * This method MUST return true for anything added with `->extend`.
     *
     * @param $handle
     *
     * @return bool
     */
    public function has($handle)
    {
        return isset($this->extensions[$handle]);
    }

    /**
     * Get the driver for this handle.
     *
     * @param string $handle
     * @param string $version
     *
     * @return ServiceInterface|null
     */
    public function getService($handle, $version = '')
    {
        $result = null;
        if ($this->has($handle)) {
            $key = $handle;
            $version = (string) $version;
            if ($version !== '') {
                $key .= "@$version";
            }
            if (!isset($this->services[$key])) {
                $abstract = $this->extensions[$handle];
                $service = $this->buildService($abstract, $version);
                if ($service === null) {
                    throw new \RuntimeException('Invalid service binding.');
                }
                $this->services[$key] = $service;
            }
            $result = $this->services[$key];
        }

        return $result;
    }

    /**
     * Build a service from an abstract.
     *
     * @param string|callable $abstract
     * @param string $version
     *
     * @return ServiceInterface|null
     */
    private function buildService($abstract, $version = '')
    {
        $resolved = null;

        if (is_string($abstract)) {
            // If it's a string, throw it at the IoC container
            $resolved = $this->app->make($abstract, array($version));
        } elseif (is_callable($abstract)) {
            // If it's a callable, lets call it with the application and $this
            $resolved = $abstract($version, $this->app, $this);
        }

        return $resolved;
    }

    /**
     * Returns all the available services.
     *
     * @return ServiceInterface[]
     */
    public function getAllServices()
    {
        $result = array();
        foreach ($this->getExtensions() as $handle) {
            $result[$handle] = $this->getService($handle);
        }

        return $result;
    }

    /**
     * Loops through the bound services and returns the ones that are active.
     *
     * @return ServiceInterface[]
     */
    public function getActiveServices()
    {
        $active = array();
        foreach ($this->getExtensions() as $handle) {
            $service = $this->getService($handle);
            $version = $service->getDetector()->detect();
            if ($version !== null) {
                $active[] = $this->getService($handle, $version);
            }
        }

        return $active;
    }
}
