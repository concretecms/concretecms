<?php

namespace Concrete\Core\Api\OAuth;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User as UserObject;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use League\OAuth2\Server\Exception\OAuthServerException;

final class Controller implements LoggerAwareInterface
{

    const STEP_LOGIN = 1;
    const STEP_AUTHORIZE_CLIENT = 2;
    const STEP_COMPLETE = 3;

    /** @var \League\OAuth2\Server\AuthorizationServer */
    private $oauthServer;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    /** @var \Psr\Http\Message\ServerRequestInterface */
    private $request;

    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;

    /** @var \Concrete\Core\Validation\CSRF\Token */
    private $token;

    /** @var \Concrete\Core\Config\Repository\Repository */
    private $config;

    /** @var \Concrete\Core\User\User The logged in user */
    private $user;

    use LoggerAwareTrait;

    public function __construct(
        AuthorizationServer $oauthServer,
        EntityManagerInterface $entityManager,
        ServerRequestInterface $request,
        Session $session,
        Token $token,
        Repository $config,
        UserObject $user = null
    ) {
        $this->oauthServer = $oauthServer;
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->session = $session;
        $this->token = $token;
        $this->config = $config;

        if ($user && $user->checkLogin()) {
            $this->user = $user;
        }
    }

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_API;
    }

    /**
     * Handle authorization
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function token()
    {
        try {
            $response = $this->oauthServer->respondToAccessTokenRequest($this->request, new Response());

            $this->pruneTokens();

            return $response;
        } catch (\Exception $e) {
            // Rethrow the exception
            throw $e;
        }
    }

    /**
     * Route handler that deals with authorization
     *
     * @return \Psr\Http\Message\ResponseInterface|\Concrete\Core\Http\Response
     */
    public function authorize()
    {
        $response = new Response();

        try {
            $request = $this->getAuthorizationRequest();
            $step = $this->determineStep($request);

            // Handle login step
            if ($step === self::STEP_LOGIN) {
                if (!$this->user) {
                    return $this->handleLogin($request);
                }

                // We have a fully logged in user, let's just use that
                $userEntity = $this->entityManager->find(User::class, $this->user->getUserID());
                $request->setUser($userEntity);
                $this->storeRequest($request);

                // Update the step
                $step = $this->determineStep($request);
            }

            // Handle authorize step
            if ($step === self::STEP_AUTHORIZE_CLIENT) {
                if ($this->getConsentType($request) !== Client::CONSENT_NONE) {
                    return $this->handleAuthorizeClient($request);
                }

                // Otherwise if consent is disabled just mark this request as approved.
                $request->setAuthorizationApproved(true);
                $this->storeRequest($request);
            }

            // We've fallen through all the other steps...
            $this->clearRequest($request);
            return $this->oauthServer->completeAuthorizationRequest($request, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $body = new LazyOpenStream('php://temp', 'r+');
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);
        }
    }

    /**
     * Handle the login portion of an authorization request
     * This method renders a view for login that handles either email or username based login
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $request
     * @return \Concrete\Core\Http\Response|\RedirectResponse
     */
    public function handleLogin(AuthorizationRequest $request)
    {
        $error = new ErrorList();
        $emailLogin = $this->config->get('concrete.user.registration.email_registration');

        while ($this->request->getMethod() === 'POST') {

            if (!$this->token->validate('oauth_login_' . $request->getClient()->getClientKey())) {
                $error->add($this->token->getErrorMessage());
                break;
            }

            $query = $this->request->getParsedBody();
            $user = array_get($query, 'uName');
            $password = array_get($query, 'uPassword');

            $userRepository = $this->entityManager->getRepository(User::class);

            /** @var User $user */
            $user = $userRepository->findOneBy([$emailLogin ? 'uEmail' : 'uName' => $user]);

            $app = Application::getFacadeApplication();
            $hasher = $app->make(\Concrete\Core\Encryption\PasswordHasher::class);

            // User successfully logged in
            if ($user && $user->getUserID() && $hasher->checkPassword($password, $user->getUserPassword())) {
                if ($hasher->needsRehash($user->getUserPassword())) {
                    $em = $app->make(EntityManagerInterface::class);

                    try {
                        $em->transactional(function () use ($user, $hasher, $password) {
                            $user->setUserPassword($hasher->hashPassword($password));
                        });
                    } catch (\Throwable $e) {
                        $this->logger->emergency('Unable to rehash password for user {user} ({id}): {message}', [
                            'user' => $user->getUserName(),
                            'id' => $user->getUserID(),
                            'message' => $e->getMessage(),
                        ]);
                    }
                }

                $userInfo = $this->entityManager->find(User::class, $user->getUserID());
                $request->setUser($userInfo);
                $this->storeRequest($request);

                return new \RedirectResponse($this->request->getUri());
            } else {
                $error->add(t('Invalid username or password.'));
            }

            break;
        }

        $contents = $this->createLoginView([
            'error' => $error,
            'auth' => $request,
            'request' => $this->request,
            'client' => $request->getClient(),
            'authorize' => false,
            'emailLogin' => $emailLogin
        ]);

        return new \Concrete\Core\Http\Response($contents->render());
    }

    /**
     * Handle the scope authorization portion of an authorization request
     * This method renders a view that outputs a list of scopes and asks the user to verify that they want to give the
     * client the access that is being requested.
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $request
     *
     * @return \Concrete\Core\Http\Response|\RedirectResponse
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function handleAuthorizeClient(AuthorizationRequest $request)
    {
        $error = new ErrorList();
        $client = $request->getClient();

        while ($this->request->getMethod() === 'POST') {

            if (!$this->token->validate('oauth_authorize_' . $client->getClientKey())) {
                $error->add($this->token->getErrorMessage());
                break;
            }

            $query = $this->request->getParsedBody();
            $authorized = array_get($query, 'authorize_client');
            $consentType = $this->getConsentType($request);

            // User authorized the request
            if ($consentType === Client::CONSENT_NONE || $authorized) {
                $request->setAuthorizationApproved(true);
                $this->storeRequest($request);

                return new \RedirectResponse($this->request->getUri());
            }

            break;
        }

        $contents = $this->createLoginView([
            'error' => $error,
            'auth' => $request,
            'request' => $this->request,
            'client' => $request->getClient(),
            'authorize' => true,
        ]);

        return new \Concrete\Core\Http\Response($contents->render());
    }

    /**
     * Prune old authentication tokens
     */
    private function pruneTokens()
    {
        $now = new \DateTime('now');
        // Delete access tokens that have no refresh token associated
        $qb = $this->entityManager->createQueryBuilder();
        $items = $qb->select('token')
            ->from(AccessToken::class, 'token')
            ->where($qb->expr()->lt('token.expiryDateTime', ':now'))
            ->getQuery()->execute([':now' => $now]);

        $this->pruneResults($items);

        // Delete all expired access tokens that have expired refresh tokens
        $qb = $this->entityManager->createQueryBuilder();
        $items = $qb->select('token')
            ->from(AccessToken::class, 'token')
            ->where($qb->expr()->lt('token.expiryDateTime', ':now'))
            ->getQuery()->execute([':now' => $now]);

        $this->pruneResults($items);

        // Delete all expired auth codes
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(AuthCode::class, 'token')
            ->where($qb->expr()->lt('token.expiryDateTime', ':now'))
            ->getQuery()->execute([':now' => $now]);
    }

    /**
     * Loop over a list of results and prune them
     *
     * @param $results
     */
    private function pruneResults(/*iterable*/ $results)
    {
        $buffer = [];
        foreach ($results as $item) {
            $buffer[] = $item;

            if (count($buffer) > 50) {
                $this->clearTokenBuffer($buffer);
                $buffer = [];
            }
        }

        $this->clearTokenBuffer($buffer);
    }

    /**
     * Remove items in a buffer from the entity manager
     *
     * @param array $buffer
     */
    private function clearTokenBuffer(array $buffer)
    {
        foreach ($buffer as $bufferItem) {
            // Clear out associated access token
            if ($bufferItem instanceof AccessToken) {
                // We have to use this method of retrieving the entity because the refresh token
                // is not accurately being set on the Access token entity for some reason.
                $refreshToken = $this->entityManager->getRepository(RefreshToken::class)
                    ->findOneByAccessToken($bufferItem);
                if ($refreshToken) {
                    $this->entityManager->remove($refreshToken);
                }
            }

            // Clear out associated refresh token
            if ($bufferItem instanceof RefreshToken) {
                $accessToken = $bufferItem->getAccessToken();
                if ($accessToken) {
                    $this->entityManager->remove($accessToken);
                }
            }

            $this->entityManager->remove($bufferItem);
        }

        $this->entityManager->flush();
    }

    /**
     * @return \League\OAuth2\Server\RequestTypes\AuthorizationRequest
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    private function getAuthorizationRequest()
    {
        $clientId = array_get($this->request->getQueryParams(), 'client_id');
        $sessionRequest = $this->restoreRequest($this->session->get("authn_request.$clientId", []));

        if ($sessionRequest) {
            return $sessionRequest;
        }

        $request = $this->oauthServer->validateAuthorizationRequest($this->request);
        $this->storeRequest($request);

        return $request;
    }

    /**
     * Get the consent type associated with the current request
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $request
     *
     * @return int
     */
    private function getConsentType(AuthorizationRequest $request)
    {
        $client = $request->getClient();
        return $client instanceof Client ? $client->getConsentType() : Client::CONSENT_SIMPLE;
    }

    /**
     * Store a request against session
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $request
     */
    private function storeRequest(AuthorizationRequest $request)
    {
        $data = [
            'user' => $request->getUser() ? $request->getUser()->getIdentifier() : null,
            'client' => $request->getClient()->getIdentifier(),
            'challenge' => $request->getCodeChallenge(),
            'challenge_method' => $request->getCodeChallengeMethod(),
            'grant_type' => $request->getGrantTypeId(),
            'redirect' => $request->getRedirectUri(),
            'scopes' => $request->getScopes(),
            'state' => $request->getState(),
            'approved' => (bool) $request->isAuthorizationApproved()
        ];

        /** @var Client $client */
        $client = $request->getClient();
        $clientId = $client->getClientKey();
        $this->session->set("authn_request.$clientId", $data);
    }

    /**
     * Restore a real request from the passed data
     *
     * @param array $data
     * @return \League\OAuth2\Server\RequestTypes\AuthorizationRequest|null
     */
    private function restoreRequest(array $data)
    {
        if (!$data) {
            return null;
        }

        // Inflate identifiers into objects
        $scopeData = (array) array_get($data, 'scopes', []);
        $scopes = array_filter(array_map([$this, 'inflateType'], $scopeData));
        $user = $this->inflateType(array_get($data, 'user'), User::class);
        $client = $this->inflateType(array_get($data, 'client'), Client::class);

        // If some data is malformed or missing, restart this whole process.
        if (!$user || !$client || count($scopes) !== count($scopeData)) {
            return null;
        }

        // Build out the authorization request
        $request = new AuthorizationRequest();
        $request->setUser($user);
        $request->setClient($client);
        $request->setAuthorizationApproved((bool) array_get($data, 'approved', false));
        $request->setCodeChallenge(array_get($data, 'challenge', ''));
        $request->setCodeChallengeMethod(array_get($data, 'challenge_method', ''));
        $request->setGrantTypeId(array_get($data, 'grant_type'));
        $request->setState(array_get($data, 'state'));
        $request->setScopes($scopes);
        $request->setRedirectUri(array_get($data, 'redirect'));

        return $request;
    }

    /**
     * Remove all session data related to this flow
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $request
     */
    private function clearRequest(AuthorizationRequest $request)
    {
        /** @var Client $client */
        $client = $request->getClient();
        $clientId = $client->getClientKey();
        $this->session->remove("authn_request.$clientId");
    }

    /**
     * Inflate an identifier into a specific type
     *
     * @param int|string|null $identifier
     * @param string $type
     * @return object|null The inflated entity
     */
    private function inflateType($identifier, $type = Scope::class)
    {
        if ($identifier === null) {
            return null;
        }

        return $this->entityManager->find($type, $identifier);
    }

    /**
     * Figure out what step we should be rendering based on the active authorization request
     * This method should handle verifying authorization and login
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $request
     * @return int
     */
    private function determineStep(AuthorizationRequest $request)
    {
        // If the request doesn't have a user attached, we need to login still.
        if (!$request->getUser()) {
            return self::STEP_LOGIN;
        }

        /** @todo Track this authorization in the database and notify the user if the scope requirements change */
        if (!$request->isAuthorizationApproved()) {
            return self::STEP_AUTHORIZE_CLIENT;
        }

        return self::STEP_COMPLETE;
    }

    /**
     * Create a new authorize login view with the given data in scope
     *
     * @param array $data
     *
     * @return \Concrete\Core\View\View
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    private function createLoginView(array $data)
    {
        // Get the client that we're rendering login for
        $consentType = $this->getConsentType($this->getAuthorizationRequest());

        switch ($consentType) {
            case Client::CONSENT_NONE:
                // Don't set anything if the consent type is "none"
                break;

            case Client::CONSENT_SIMPLE:
            default:
                $data['consentView'] = new View('/oauth/consent/simple');
                break;
        }

        // Start building a view:
        $contents = new View('/oauth/authorize');
        $contents->addScopeItems($data);

        return $contents;
    }

}
