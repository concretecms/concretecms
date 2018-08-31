<?php
namespace Concrete\Core\Http;

use Concrete\Core\Routing\Router;
use Concrete\Core\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Concrete\Core\Routing\Route;

class RouteDispatcher implements DispatcherInterface
{

    /** @var Route */
    protected $route;

    /** @var Router */
    protected $router;

    /** @var array */
    private $parameters;


    public function __construct(RouterInterface $router, Route $route, array $parameters)
    {
        $this->router = $router;
        $this->route = $route;
        $this->parameters = $parameters;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return SymfonyResponse
     */
    public function dispatch(SymfonyRequest $request)
    {
        $action = $this->router->resolveAction($this->route);
        $response = $action->execute($request, $this->route, $this->parameters);
        return $response;
    }


}
