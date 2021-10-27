<?php

namespace Concrete\Core\Api;

use Concrete\Core\Api\OAuth\Scope\ScopeRegistry;
use Concrete\Core\Api\OAuth\Scope\ScopeRegistryInterface;
use Concrete\Core\Api\OAuth\Server\IdTokenResponse;
use Concrete\Core\Api\OAuth\Validator\DefaultValidator;
use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Entity\OAuth\UserRepository;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Routing\Router;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use phpseclib3\Crypt\RSA;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\CryptKey;

class ApiServiceProvider extends ServiceProvider
{

    const KEY_PRIVATE = 'privatekey';
    const KEY_PUBLIC = 'publickey';

    private $keyPair;

    /**
     * Register API related stuff
     *
     * @return void
     */
    public function register()
    {
        $config = $this->app->make("config");
        if ($this->app->isInstalled() && $config->get('concrete.api.enabled')) {
            $router = $this->app->make(Router::class);
            $list = new ApiRouteList();
            $list->loadRoutes($router);
            $this->registerAuthorizationServer();
        }
        $this->app->singleton(ScopeRegistryInterface::class, function() {
            return new ScopeRegistry();
        });
        
        // Provide our public key to the BearerTokenValidator
        $this->app->extend(BearerTokenValidator::class, function(BearerTokenValidator $validator) {
            if (method_exists($validator, 'setPublicKey')) {
                $key = (string) $this->getKey(self::KEY_PUBLIC);
                $validator->setPublicKey(new CryptKey($key));
            }

            return $validator;
        });
    }

    private function repositoryFactory($factoryClass, $entityClass)
    {
        return function () use ($factoryClass, $entityClass) {
            $em = $this->app->make(EntityManagerInterface::class);
            $metadata = $em->getClassMetadata($entityClass);

            return $this->app->make($factoryClass, [
                'em' => $em,
                'class' => $metadata,
            ]);
        };
    }

    private function repositoryFor($class)
    {
        return function () use ($class) {
            $em = $this->app->make(EntityManagerInterface::class);
            return $em->getRepository($class);
        };
    }

    /**
     * Generate new RSA keys if needed
     * @return string[] ['privatekey' => '...', 'publickey' => '...']
     */
    private function getKeyPair()
    {
        $config = $this->app->make('config/database');

        // Seee if we already have a kypair
        $keyPair = $config->get('api.keypair');

        if (!$keyPair) {
            // Generate a new RSA key
            $privateKey = RSA::createKey(2048);
            $publicKey = $privateKey->getPublicKey();

            $keyPair = [self::KEY_PRIVATE=>str_replace("\r\n","\n",$privateKey->__toString()),self::KEY_PUBLIC=>str_replace("\r\n","\n",$publicKey->__toString())];

            // Save the keypair
            $config->save('api.keypair', $keyPair);
        }

        return $keyPair;
    }

    /**
     * Get a key by handle
     * @param string $handle privatekey | publickey
     * @return string|null
     */
    private function getKey(string $handle)
    {
        if (!$this->keyPair) {
            $this->keyPair = $this->getKeyPair();
        }

        return isset($this->keyPair[$handle]) ? $this->keyPair[$handle] : null;
    }

    /**
     * Register the authorization and authentication server classes
     */
    protected function registerAuthorizationServer()
    {
        // The ResourceServer deals with authenticating requests, in other words validating tokens
        $this->app->bind(ResourceServer::class, function() {
            $cryptKey = new CryptKey($this->getKey(self::KEY_PUBLIC), null, DIRECTORY_SEPARATOR !== '\\');
            return new ResourceServer(
                $this->app->make(AccessTokenRepositoryInterface::class),
                $cryptKey,
                $this->app->make(DefaultValidator::class)
            );
        });

        // AuthorizationServer on the other hand deals with authorizing a session with a username and password and key and secret
        $this->app->when(AuthorizationServer::class)->needs('$privateKey')->give($this->getKey(self::KEY_PRIVATE));
        $this->app->when(AuthorizationServer::class)->needs('$publicKey')->give($this->getKey(self::KEY_PUBLIC));
        $this->app->when(AuthorizationServer::class)->needs('$encryptionKey')->give($this->app->make('config/database')->get('concrete.security.token.encryption'));
        $this->app->when(AuthorizationServer::class)->needs(ResponseTypeInterface::class)->give(function() {
            return $this->app->make(IdTokenResponse::class);
        });

        $this->app->extend(AuthorizationServer::class, function (AuthorizationServer $server) {

            $oneHourTTL = new \DateInterval('PT1H');
            $oneDayTTL = new \DateInterval('P1D');


            $config = $this->app->make('config');

            if ($config->get('concrete.api.grant_types.password_credentials')) {
                $server->enableGrantType($this->app->make(PasswordGrant::class), $oneHourTTL);
            }
            if ($config->get('concrete.api.grant_types.client_credentials')) {
                $server->enableGrantType($this->app->make(ClientCredentialsGrant::class), $oneHourTTL);
            }
            if ($config->get('concrete.api.grant_types.authorization_code')) {
                $server->enableGrantType($this->app->make(AuthCodeGrant::class, ['authCodeTTL' => $oneDayTTL]), $oneDayTTL);
            }
            if ($config->get('concrete.api.grant_types.refresh_token')) {
                $server->enableGrantType($this->app->make(RefreshTokenGrant::class), $oneHourTTL);
            }
            return $server;
        });

        // Register OAuth stuff
        $this->app->bind(AccessTokenRepositoryInterface::class, $this->repositoryFor(AccessToken::class));
        $this->app->bind(AuthCodeRepositoryInterface::class, $this->repositoryFor(AuthCode::class));
        $this->app->bind(ClientRepositoryInterface::class, $this->repositoryFor(Client::class));
        $this->app->bind(RefreshTokenRepositoryInterface::class, $this->repositoryFor(RefreshToken::class));
        $this->app->bind(ScopeRepositoryInterface::class, $this->repositoryFor(Scope::class));
        $this->app->bind(UserRepositoryInterface::class, $this->repositoryFactory(UserRepository::class, User::class));
    }

}
