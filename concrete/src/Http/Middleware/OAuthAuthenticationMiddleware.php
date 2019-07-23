<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Http\PSR7\GuzzleFactory;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;

class OAuthAuthenticationMiddleware implements MiddlewareInterface
{

    /**
     * @var \League\OAuth2\Server\ResourceServer
     */
    private $oauth;

    /**
     * @var \Concrete\Core\Http\PSR7\GuzzleFactory
     */
    private $psrFactory;

    /**
     * @var \Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory
     */
    private $foundationFactory;

    /**
     * @var \Concrete\Core\Http\Middleware\Application
     */
    private $app;

    public function __construct(
        ResourceServer $oauth,
        GuzzleFactory $psrFactory,
        UserInfoRepository $userRepository,
        HttpFoundationFactory $foundationFactory
    ) {
        $this->oauth = $oauth;
        $this->psrFactory = $psrFactory;
        $this->foundationFactory = $foundationFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * Process the request and return a response
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface $frame
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $route = $request->attributes->get('_route');
        $psrRequest = $this->psrFactory->createRequest($request);

        // Validate the request. This throws exceptions on failure
        $psrRequest = $this->oauth->validateAuthenticatedRequest($psrRequest);
        $newRequest = $this->foundationFactory->createRequest($psrRequest);

        // Merge in attributes to existing request object
        $request->attributes->add($newRequest->attributes->all());

        // Handle route scope
        if ($route) {
            if ($routeScopes = $route->getOption('oauth_scopes')) {
                $requestScopes = $request->attributes->get('oauth_scopes');

                if (!array_intersect((array)$routeScopes, (array)$requestScopes)) {
                    throw new OAuthServerException(
                        'Endpoint out of scope.', 1, 'invalid_request', 400, 'Try reauthorizing with the proper scope.'
                    );
                }
            }

            if ($userId = $request->attributes->get('oauth_user_id')) {
                /**
                 * @var $request \Concrete\Core\Http\Request
                 */
                $request->setCustomRequestUser(
                    $this->userRepository->getByID($userId)
                );
            }
        }

        return $frame->next($request);
    }
}
