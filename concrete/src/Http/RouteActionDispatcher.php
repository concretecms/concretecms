<?php
namespace Concrete\Core\Http;

use Concrete\Core\Routing\Router;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Routing\Route;

class RouteActionDispatcher implements DispatcherInterface
{

    /** @var Route */
    protected $route;

    /** @var Router */
    protected $router;

    public function __construct(Router $router, Route $route)
    {
        $this->router = $router;
        $this->route = $route;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return SymfonyResponse
     */
    public function dispatch(SymfonyRequest $request)
    {
        $action = $this->router->getAction($this->route);
        $response = $action->execute($request, $this->route, []);
        return $response;
    }


}
