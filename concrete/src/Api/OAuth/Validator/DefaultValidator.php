<?php

namespace Concrete\Core\Api\OAuth\Validator;

use Concrete\Core\Application\Application;
use Concrete\Core\User\User;
use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\CryptKey;
use Psr\Http\Message\ServerRequestInterface;

class DefaultValidator implements AuthorizationValidatorInterface
{

    /** @var \League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface */
    private $validator;

    /** @var \Concrete\Core\Application\Application */
    private $app;

    public function __construct(BearerTokenValidator $validator, Application $app)
    {
        $this->validator = $validator;
        $this->app = $app;
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
        return $this->validator->validateAuthorization($request);
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
