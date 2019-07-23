<?php
namespace Concrete\Core\Routing;

use Symfony\Component\Routing\Route as SymfonyRoute;

class Route extends SymfonyRoute
{

    /**
     * If this route has a custom name, it appears here. Otherwise it is automatically generated
     * from the path.
     * @var string
     */
    protected $customName;

    /**
     * The action that the route will execute when it is run.
     * This could be a callback, a controller string, or something else.
     * It is the job of the RouteActionFactory to turn it from whatever
     * it currently is into RouteAction object.
     * @var mixed
     */
    protected $action;

    /**
     * @var RouteMiddleware[]
     */
    protected $middlewares = [];

    /**
     * @return bool
     */
    public function hasCustomName()
    {
        return isset($this->customName);
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if (isset($this->customName)) {
            return $this->customName;
        } else {
            return $this->getGeneratedName();
        }
    }

    /**
     * Sets the custom name. Note: if the route has already been added to the route
     * collection you will want to use $route->updateName($name, $router)
     * instead
     * @param $name
     */
    public function setCustomName($name)
    {
        $this->customName = $name;
    }

    /**
     * @param mixed $name
     */
    public function updateName($name, Router $router)
    {
        $router->getRoutes()->remove($this->getName());
        $this->setCustomName($name);
        $router->getRoutes()->add($name, $this);
    }

    private function getGeneratedName()
    {
        $methods = $this->getMethods();
        if (count($methods) == 7) {
            $methodName = 'all';
        } else {
            $methodName = strtolower(implode('_', $methods));
        }
        $path = $this->getPath();
        $path = trim($path, '/');
        $name = preg_replace('/[^A-Za-z0-9\_]/', '_', $path);
        $name = preg_replace('/\_+/', '_', $name);
        $name = trim($name, '_');
        $name .= '_' . $methodName;
        return $name;
    }

    /**
     * Adds middleware to the route.
     * 
     * @param RouteMiddleware $middleware
     */
    public function addMiddleware(RouteMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return RouteMiddleware[]
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * Explicitly sets an OAuth2 scope to a route. This will be used if the route is consumed in an
     * OAuth2 request.
     * 
     * @param string $scope
     */
    public function setScopes($scope)
    {
        $this->setOption('oauth_scopes', $scope);
    }

}
