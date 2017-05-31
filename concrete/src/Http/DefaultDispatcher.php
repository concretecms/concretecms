<?php
namespace Concrete\Core\Http;

use Concrete\Core\Application\Application;
use Concrete\Core\Routing\DispatcherRouteCallback;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

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
        $session = $this->app['session'];

        if (!$session->has('uID')) {
            User::verifyAuthTypeCookie();
        }

        // User may have been logged in, so lets check status again.
        if ($session->has('uID') && $session->get('uID') > 0 && $response = $this->validateUser()) {
            return $response;
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
        $collection = $this->router->getList();
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($collection, $context);
        $path = rtrim($request->getPathInfo(), '/') . '/';

        $callDispatcher = false;
        try {
            $request->attributes->add($matcher->match($path));
            $matched = $matcher->match($path);
            $route = $collection->get($matched['_route']);

            $this->router->setRequest($request);
            $response = $this->router->execute($route, $matched);
        } catch (ResourceNotFoundException $e) {
            $callDispatcher = true;
        } catch (MethodNotAllowedException $e) {
            $callDispatcher = true;
        }
        if ($callDispatcher) {
            $callback = $this->app->make(DispatcherRouteCallback::class, ['dispatcher']);
            $response = $callback->execute($request);
        }

        return $response;
    }
}
