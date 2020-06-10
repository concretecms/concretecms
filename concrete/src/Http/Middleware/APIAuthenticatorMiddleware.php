<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\Application;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\User\UserInfoRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Concrete\Core\Http\Request as ConcreteRequest;
use Concrete\Core\Authentication\OAuth2\Request as OAuth2Request;

class APIAuthenticatorMiddleware implements MiddlewareInterface
{

    protected $app;
    protected $oauth;
    protected $factory;
    private $repository;
    protected $logger;

    public function __construct(Application $app, ResponseFactory $factory, UserInfoRepository $repository, LoggerInterface $logger)
    {
        $this->app = $app;
        $this->factory = $factory;
        $this->oauth = $this->app->make('oauth2/server');
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * Process the request and return a response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return mixed
     */
    public function process(Request $request, DelegateInterface $frame)
    {

        $wrappedRequest = new OAuth2Request($request);
        $scope = null;

        if (!$this->oauth->verifyResourceRequest($wrappedRequest, $response = new \OAuth2\Response, $scope)) {
            $body = $response->getParameters();
            if (!$body && $response->getStatusCode() == 401) {
                $body = [
                    'Not Authenticated'
                ];
                $this->logger->warning(t('Access to API not allowed.'));
            }
            return $this->factory->json($body, $response->getStatusCode(),
                $response->getHttpHeaders());
        }

        $token = $this->oauth->getAccessTokenData($wrappedRequest);
        if ($id = array_get($token, 'user_id')) {
            $req = ConcreteRequest::getInstance();
            $req->setCustomRequestUser($this->repository->getByID($id)); // we need this for permissions.
        }


        $this->logger->info(t('Running API method: %s', $wrappedRequest->request->getPathInfo()));

        return $frame->next($request);

    }


}