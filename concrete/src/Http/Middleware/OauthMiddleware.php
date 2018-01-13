<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\User\UserInfoRepository;
use Symfony\Component\HttpFoundation\Request;

class OAuthMiddleware implements MiddlewareInterface
{

    protected $app;
    protected $oauth;
    protected $factory;
    private $repository;

    public function __construct(Application $app, ResponseFactory $factory, UserInfoRepository $repository)
    {
        $this->app = $app;
        $this->factory = $factory;
        $this->repository = $repository;
    }

    /**
     * Process the request and return a response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return mixed
     */
    public function process(Request $request, DelegateInterface $frame)
    {
/*
        $wrappedRequest = new WrappedRequest($request);
        $scope = null;

        // Early return if a route has disabled oauth
        if ($route = $request->attributes->get('route')) {
            if ($route->getOption('oauth') === false) {
                return $frame->next($request);
            }

            if ($routeScope = $route->getOption('oauth_scope')) {
                $scope = $routeScope;
            }
        }


        if (!$this->oauth->verifyResourceRequest($wrappedRequest, $response = new \OAuth2\Response, $scope)) {
            $body = $response->getParameters();
            if (!$body && $response->getStatusCode() == 401) {
                $body = [
                    'Not Authenticated'
                ];
            }
            return $this->factory->json($body, $response->getStatusCode(),
                $response->getHttpHeaders());
        }

        $token = $this->oauth->getAccessTokenData($wrappedRequest);
        if ($id = array_get($token, 'user_id')) {
            $request->attributes->set('user', $this->repository->getByName($id));
        }
*/

        return $frame->next($request);

    }


}