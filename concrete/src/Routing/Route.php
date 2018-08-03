<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Http\Middleware\MiddlewareInterface;

class Route extends \Symfony\Component\Routing\Route
{

    /**
     * @var MiddlewareInterface[][]
     */
    protected $middleware = [];

    /**
     * @var string[]
     */
    protected $scopes = [];

    /**
     * Get the associated callback
     *
     * @return callable|mixed
     */
    public function getCallback()
    {
        $defaults = $this->getDefaults();

        return $defaults['callback'];
    }

    /**
     * Get the associated path
     *
     * @return string|mixed
     */
    public function getPath()
    {
        if ($path = parent::getPath()) {
            return $path;
        }
        $defaults = $this->getDefaults();

        return $defaults['path'];
    }

    /**
     * Get the middlewares associated with this route
     *
     * @return \Concrete\Core\Http\Middleware\MiddlewareInterface[][] An array of middleware arrays keyed by priority
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * Add a middleware to be processed with this route
     *
     * @param \Concrete\Core\Http\Middleware\MiddlewareInterface|string $middleware
     * @param int $priority
     * @return \Concrete\Core\Routing\Route
     */
    public function addMiddleware($middleware, $priority = 10)
    {
        if (!isset($this->middleware[$priority])) {
            $this->middleware[$priority] = [];
        }

        $this->middleware[$priority][] = $middleware;
        return $this;
    }

    /**
     * Get allowed scopes
     *
     * @return string[]
     */
    public function getScopes()
    {
        return $this->getOption('oauth_scopes');
    }

    /**
     * Set the allowed scopes for this route
     *
     * @param string[] $scopes
     * @return $this
     */
    public function setScopes($scopes)
    {
        $this->setOption('oauth_scopes', $scopes);
        return $this;
    }

    /**
     * Add an allowed scope
     *
     * @param $scope
     * @return $this
     */
    public function addScope($scope)
    {
        $scopes = $this->getScopes();
        $scopes[] = $scope;
        return $this->setScopes($scopes);
    }
}
