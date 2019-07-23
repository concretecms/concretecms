<?php

namespace Concrete\Core\Install;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Exception;

/**
 * Service class for installation preconditions.
 */
class PreconditionService
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * The configuration repository.
     *
     * @var Repository
     */
    protected $config;

    /**
     * Initializes the instance.
     *
     * @param Application $app the application instance
     * @param Repository $config the configuration repository
     */
    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * Get the pre-configuration preconditions.
     *
     * @param bool $includeWebPreconditions
     *
     * @return PreconditionInterface[]
     */
    public function getPreconditions($includeWebPreconditions = true)
    {
        $result = [];
        foreach ($this->getAllPreconditions() as $instance) {
            if (!$instance instanceof OptionsPreconditionInterface) {
                if ($includeWebPreconditions || !$instance instanceof WebPreconditionInterface) {
                    $result[] = $instance;
                }
            }
        }

        return $result;
    }

    /**
     * Get the post-configuration preconditions.
     *
     * @return OptionsPreconditionInterface[]
     */
    public function getOptionsPreconditions()
    {
        $result = [];
        foreach ($this->getAllPreconditions() as $instance) {
            if ($instance instanceof OptionsPreconditionInterface) {
                $result[] = $instance;
            }
        }

        return $result;
    }

    /**
     * Get a precondition given its handle.
     *
     * @param string $handle the precondition handle
     *
     * @throws Exception
     *
     * @return PreconditionInterface
     */
    public function getPreconditionByHandle($handle)
    {
        $list = $this->config->get('install.preconditions');
        if (!isset($list[$handle])) {
            throw new Exception(sprintf('Unable to find an install precondition with handle %s', $handle));
        }
        if (!$list[$handle]) {
            throw new Exception(sprintf('The precondition with handle %s is disabled', $handle));
        }
        $className = $list[$handle];
        $instance = $this->getPreconditionByClassName($className);

        return $instance;
    }

    /**
     * Get a precondition given its fully-qualified class name.
     *
     * @param string $className the fully-qualified class name of the precondition
     *
     * @throws Exception
     *
     * @return PreconditionInterface
     */
    public function getPreconditionByClassName($className)
    {
        if (!class_exists($className, true)) {
            throw new Exception(sprintf('The precondition class %s does not exist', $className));
        }
        $result = $this->app->make($className);
        if (!$result instanceof PreconditionInterface) {
            throw new Exception(sprintf('The class %1$s should implement the interface %2$s', $className, PreconditionInterface::class));
        }

        return $result;
    }

    /**
     * Get all the defined preconditions, of any kind.
     *
     * @return PreconditionInterface[]
     */
    private function getAllPreconditions()
    {
        $result = [];
        $list = $this->config->get('install.preconditions');
        foreach ($list as $className) {
            if ($className) {
                $result[] = $this->getPreconditionByClassName($className);
            }
        }

        return $result;
    }
}
