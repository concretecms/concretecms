<?php

namespace Concrete\Core\Api\OAuth\Validator;

use Concrete\Core\Api\OAuth\UserStatusValidator;
use Concrete\Core\Application\Application;
use Concrete\Core\User\User;
use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class DefaultValidator implements AuthorizationValidatorInterface
{

    /** @var \League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface */
    private $validator;

    /** @var \Concrete\Core\Application\Application */
    private $app;

    /** @var \Concrete\Core\Api\OAuth\UserStatusValidator */
    private $userStatusValidator;

    public function __construct(
        BearerTokenValidator $validator,
        Application $app,
        UserStatusValidator $userStatusValidator
    ) {
        $this->validator = $validator;
        $this->app = $app;
        $this->userStatusValidator = $userStatusValidator;
    }

    /**
     * Determine the access token in the authorization header and append OAUth properties to the request
     *  as attributes.
     *
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     */
    public function validateAuthorization(ServerRequestInterface $request)
    {
        $user = $this->app->make(User::class);

        // Allow logged in users to bypass API authentication entirely if the route allows it
        // This functionality is NOT READY. We will not allow this yet.
        /*
        $route = $request->getAttribute('_route');

        if ($user->checkLogin()) {
            // Return the request with additional attributes
            return $request
                ->withAttribute('oauth_access_token_id', null)
                ->withAttribute('oauth_client_id', null)
                ->withAttribute('oauth_user_id', null)
                ->withAttribute('oauth_scopes', 'session');

            return $request;
        }
        */

        // Delegate the rest to the passed in validator
        $request = $this->validator->validateAuthorization($request);

        // A valid bearer token only proves that a token was issued and has not expired or been
        // revoked - it says nothing about the state of the account it was issued to. Re-check the user
        // here so that deactivated, deleted and password-reset accounts lose API access immediately
        // rather than for the remaining lifetime of the token. Tokens issued through the client
        // credentials grant are not tied to a user and carry an empty subject.
        $userIdentifier = $request->getAttribute('oauth_user_id');

        if ($userIdentifier && !$this->userStatusValidator->isValid($userIdentifier)) {
            throw OAuthServerException::accessDenied('Token is not linked to an active user');
        }

        return $request;
    }

    /**
     * @param CryptKey $key
     */
    public function setPublicKey(CryptKey $key)
    {
        $this->validator->setPublicKey($key);
    }

    /**
     * Set path to private key.
     *
     * @param CryptKey $privateKey
     */
    public function setPrivateKey(CryptKey $privateKey)
    {
        $this->validator->setPrivateKey($privateKey);
    }

    /**
     * Set the encryption key
     *
     * @param string $key
     */
    public function setEncryptionKey($key = null)
    {
        $this->validator->setEncryptionKey($key);
    }
}
