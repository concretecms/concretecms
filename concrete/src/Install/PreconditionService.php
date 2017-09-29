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
     * Get the preconditions.
     *
     * @param bool $includeWebPreconditions
     *
     * @return PreconditionInterface[]
     */
    public function getPreconditions($includeWebPreconditions = true)
    {
        $result = [];
        $handles = $this->config->get('install.preconditions');
        foreach ($handles as $handle) {
            $prefix = false;
            if (is_array($handle)) {
                if (isset($handle[1])) {
                    list($handle, $prefix) = $handle;
                } else {
                    $handle = $handle[0];
                }
            }
            $instance = $this->getPreconditionByHandle($handle, $prefix);
            if ($includeWebPreconditions || !$instance instanceof WebPreconditionInterface) {
                $result[] = $instance;
            }
        }

        return $result;
    }

    /**
     * Get a precondition given its handle.
     *
     * @param string $handle the precondition handle
     * @param bool|string $prefix The class prefix
     *
     * @throws Exception
     *
     * @return PreconditionInterface
     */
    public function getPreconditionByHandle($handle, $prefix = false)
    {
        $baseClassName = 'Core\\Install\\Preconditions\\' . camelcase($handle);
        $className = core_class($baseClassName, $prefix);
        if (!class_exists($className, true)) {
            throw new Exception(sprintf('Unable to find the class %s', $className));
        }
        $instance = $this->app->make($className);
        if (!$instance instanceof PreconditionInterface) {
            throw new Exception(sprintf('The class %1$s should implement the interface %2$s', get_class($instance), PreconditionInterface::class));
        }

        return $instance;
    }
}
