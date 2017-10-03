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
        $list = $this->config->get('install.preconditions');
        foreach ($list as $className) {
            if (!$className) {
                continue;
            }
            $instance = $this->app->make($className);
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
     *
     * @throws Exception
     *
     * @return PreconditionInterface
     */
    public function getPreconditionByHandle($handle)
    {
        $list = $this->config->get('install.preconditions');
        if (!isset($list[$handle])) {
            throw new Exception(sprintf('Unable to an install precondition with handle %s', $handle));
        }
        if (!$list[$handle]) {
            throw new Exception(sprintf('The precondition with handle %s is disabled', $handle));
        }
        $className = $list[$handle];
        $instance = $this->app->make($className);

        return $instance;
    }
}
