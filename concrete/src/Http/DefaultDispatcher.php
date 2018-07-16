<?php

namespace Concrete\Core\Http;

use Concrete\Core\Application\Application;
use Concrete\Core\Routing\DispatcherRouteCallback;
use Concrete\Core\Routing\Redirect;
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
     * @var \Concrete\Core\Routing\RouterInterface
     */
    private $router;

    public function __construct(Application $app, RouterInterface $router)
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
            throw new \RuntimeException(t('Invalid path traversal. Please make this request with a valid HTTP client.'));
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
        $user = new User();
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
        $path = rtrim($request->getPathInfo(), '/') . '/';
        $collection = $this->router->getList();
        $collection = $this->filterRouteCollectionForPath($collection, $path);
        if ($collection->count() === 0) {
            $callDispatcher = true;
        } else {
            $context = new RequestContext();
            $context->fromRequest($request);
            $matcher = new UrlMatcher($collection, $context);
            $callDispatcher = false;
            try {
                $matched = $matcher->match($path);
                $request->attributes->add($matched);
                $route = $collection->get($matched['_route']);
                $this->router->setRequest($request);
                $response = $this->router->execute($route, $matched);
            } catch (ResourceNotFoundException $e) {
                $callDispatcher = true;
            } catch (MethodNotAllowedException $e) {
                $callDispatcher = true;
            }
        }
        if ($callDispatcher) {
            $callback = $this->app->make(DispatcherRouteCallback::class, ['dispatcher']);
            $response = $callback->execute($request);
        }

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
