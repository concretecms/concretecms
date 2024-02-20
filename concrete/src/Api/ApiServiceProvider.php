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
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\AuthorizationServer;
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
use League\OAuth2\Server\Grant\ClientCredentialsGrant;

class ApiServiceProvider extends ServiceProvider
{
    const KEY_PRIVATE = 'privatekey';

    const KEY_PUBLIC = 'publickey';

    /**
     * @var \Concrete\Core\Api\CryptKeyFactory|null
     */
    private $cryptKeyFactory;

    /**
     * Register API related stuff
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ScopeRegistryInterface::class, static function() {
            return new ScopeRegistry();
        });
        if ($this->app->isInstalled() && $this->app->make('config')->get('concrete.api.enabled')) {
            $router = $this->app->make(Router::class);
            $list = new ApiRouteList();
            $list->loadRoutes($router);
            $this->registerAuthorizationServer();
        }
    }

    /**
     * Register the authorization and authentication server classes
     */
    protected function registerAuthorizationServer()
    {
        // The ResourceServer deals with authenticating requests, in other words validating tokens
        $this->app->bind(ResourceServer::class, function() {
            return $this->app->build(ResourceServer::class, [
                $this->app->make(AccessTokenRepositoryInterface::class),
                $this->getCryptKey(static::KEY_PUBLIC),
                $this->app->make(DefaultValidator::class)
            ]);
        });

        // AuthorizationServer on the other hand deals with authorizing a session with a username and password and key and secret
        $this->app->when(AuthorizationServer::class)->needs('$privateKey')->give(function() {
            return $this->getCryptKey(static::KEY_PRIVATE);
        });
        $this->app->when(AuthorizationServer::class)->needs('$publicKey')->give(function() {
            return $this->getCryptKey(static::KEY_PUBLIC);
        });
        $this->app->when(AuthorizationServer::class)->needs(ResponseTypeInterface::class)->give(function() {
            return $this->app->make(IdTokenResponse::class);
        });
        $this->app->extend(AuthorizationServer::class, function (AuthorizationServer $server) {
            $server->setEncryptionKey($this->app->make('config/database')->get('concrete.security.token.encryption'));
            $oneHourTTL = new DateInterval('PT1H');
            $oneDayTTL = new DateInterval('P1D');
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

    /**
     * Get a key by handle
     *
     * @param string $handle ApiServiceProvider::KEY_PRIVATE | ApiServiceProvider::KEY_PUBLIC
     *
     * @return \League\OAuth2\Server\CryptKey
     */
    private function getCryptKey($handle)
    {
        if ($this->cryptKeyFactory === null) {
            $this->cryptKeyFactory = $this->app->make(CryptKeyFactory::class);
        }
        return $this->cryptKeyFactory->getCryptKey($handle);
    }

    private function repositoryFactory($factoryClass, $entityClass)
    {
        return function () use ($factoryClass, $entityClass) {
            $em = $this->app->make(EntityManagerInterface::class);
            $metadata = $em->getClassMetadata($entityClass);

            return $this->app->make($factoryClass, [
                $em,
                $metadata
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
}
