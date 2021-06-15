<?php

namespace Concrete\Core\Http;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Middleware\DispatcherDelegate;
use Concrete\Core\Http\Middleware\MiddlewareStack;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Routing\Router;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\Session\SessionValidator;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class DefaultDispatcher implements DispatcherInterface
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    private $app;

    /**
     * @var \Concrete\Core\Routing\Router
     */
    private $router;

    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return SymfonyResponse
     */
    public function dispatch(SymfonyRequest $request)
    {
        $path = rawurldecode($request->getPathInfo());

        if (substr($path, 0, 3) == '../' || substr($path, -3) == '/..' || strpos($path, '/../') ||
            substr($path, 0, 3) == '..\\' || substr($path, -3) == '\\..' || strpos($path, '\\..\\')) {
            throw new UserMessageException(t('Invalid path traversal. Please make this request with a valid HTTP client.'));
        }

        $response = null;
        if ($this->app->isInstalled()) {
            $response = $this->getEarlyDispatchResponse();
        }
        if ($response === null) {
            $response = $this->handleDispatch($request);
        }

        return $response;
    }

    private function getEarlyDispatchResponse()
    {
        $validator = $this->app->make(SessionValidator::class);
        if ($validator->hasActiveSession()) {
            $session = $this->app['session'];
            if (!$session->has('uID')) {
                User::verifyAuthTypeCookie();
            }

            // User may have been logged in, so lets check status again.
            if ($session->has('uID') && $session->get('uID') > 0 && $response = $this->validateUser()) {
                return $response;
            }
        }
    }

    private function validateUser()
    {
        // check to see if this is a valid user account
        $user = $this->app->make(User::class);
        if (!$user->checkLogin()) {
            $isActive = $user->isActive();
            $user->logout();

            if ($user->isError()) {
                switch ($user->getError()) {
                    case USER_SESSION_EXPIRED:
                        return Redirect::to('/login', 'session_invalidated')->send();
                }
            } elseif (!$isActive) {
                return Redirect::to('/login', 'account_deactivated')->send();
            } else {
                $v = new View('/frontend/user_error');
                $v->setViewTheme('concrete');
                $contents = $v->render();

                return $this->app->make(ResponseFactoryInterface::class)->forbidden($contents);
            }
        }
    }

    private function handleDispatch($request)
    {
        try {
            $route = $this->router->matchRoute($request)->getRoute();
            $dispatcher = new RouteDispatcher($this->router, $route, []);
            $stack = new MiddlewareStack(
                new DispatcherDelegate($dispatcher)
            );
            $stack->setApplication($this->app);
            foreach($route->getMiddlewares() as $middleware) {
                if (is_string($middleware->getMiddleware())) {
                    $inflatedMiddleware =  $this->app->make($middleware->getMiddleware());
                } else {
                    $inflatedMiddleware = $middleware->getMiddleware();
                }
                $stack = $stack->withMiddleware(
                    $inflatedMiddleware,
                    $middleware->getPriority()
                );
            }
            return $stack->process($request);
        } catch (ResourceNotFoundException $e) {
        } catch (MethodNotAllowedException $e) {
        }
        $c = \Page::getFromRequest($request);
        $response = $this->app->make(ResponseFactoryInterface::class)->collection($c);

        return $response;
    }

    /**
     * @param \Symfony\Component\Routing\RouteCollection $routes
     * @param string $path
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    private function filterRouteCollectionForPath(RouteCollection $routes, $path)
    {
        $result = new RouteCollection();
        foreach ($routes->getResources() as $resource) {
            $result->addResource($resource);
        }
        foreach ($routes->all() as $name => $route) {
            $routePath = $route->getPath();
            $p = strpos($routePath, '{');
            $skip = false;
            if ($p === false) {
                if ($routePath !== $path) {
                    $skip = true;
                }
            } elseif ($p > 0) {
                $routeFixedPath = substr($routePath, 0, $p);
                if (strpos($path, $routeFixedPath) !== 0) {
                    $skip = true;
                }
            }
            if ($skip === false) {
                $result->add($name, $route);
            }
        }

        return $result;
    }
}
