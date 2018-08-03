<?php

namespace Concrete\Core\Http;

use Concrete\Core\Routing\Route;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RouteDispatcher implements DispatcherInterface
{

    /** @var \Concrete\Core\Routing\Route */
    private $route;

    /** @var array */
    private $parameters;

    public function __construct(Route $route, array $parameters)
    {
        $this->route = $route;
        $this->parameters = $parameters;
    }

    /**
     * Take a request and populate the provided response.
     * Optionally this method may return new intances of Response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return SymfonyResponse
     */
    public function dispatch(SymfonyRequest $request)
    {
        $callback = $this->route->getCallback();
        $response = $callback->execute($request, $this->route, $this->parameters);

        return $response;
    }
}
