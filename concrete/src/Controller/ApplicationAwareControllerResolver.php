<?php
namespace Concrete\Core\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as SymfonyControllerResolver;
use Concrete\Core\Application\ApplicationAwareTrait;

class ApplicationAwareControllerResolver extends SymfonyControllerResolver implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(Application $app, ?LoggerInterface $logger = null)
    {
        $this->setApplication($app);
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * This method looks for a '_controller' request attribute that represents
     * the controller name (a string like ClassName::MethodName).
     *
     * @api
     */
    public function getController(Request $request)
    {
        if (!$controller = $request->attributes->get('_controller')) {
            if (null !== $this->logger) {
                $this->logger->warning('Unable to look for the controller as the "_controller" parameter is missing');
            }

            return false;
        }

        if (is_array($controller)) {
            return $controller;
        }

        if (is_object($controller)) {
            if (method_exists($controller, '__invoke')) {
                return [$controller, '__invoke'];
            }

            throw new \InvalidArgumentException(sprintf('Controller "%s" for URI "%s" is not callable.', get_class($controller), $request->getPathInfo()));
        }

        if (false === strpos($controller, ':')) {
            if (method_exists($controller, '__invoke')) {
                return [$this->app->make($controller), '__invoke'];
            }
            if (function_exists($controller)) {
                return [new FunctionController($controller), '__invoke'];
            }
        }

        $callable = $this->createController($controller);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf('Controller "%s" for URI "%s" is not callable.', $controller, $request->getPathInfo()));
        }

        return $callable;
    }

    /**
     * Returns a callable for the given controller.
     *
     * @param string $controller A Controller string
     *
     * @return mixed A PHP callable
     *
     * @throws \InvalidArgumentException
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        [$class, $method] = explode('::', $controller, 2);

        if (!class_exists($class) && !$this->app->bound($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        return [$this->app->make($class), $method];
    }
}
