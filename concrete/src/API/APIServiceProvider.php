<?php

namespace Concrete\Core\API;

use Concrete\Core\Entity\OAuth\AccessToken;
use Concrete\Core\Entity\OAuth\AuthCode;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\RefreshToken;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Entity\OAuth\UserRepository;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Http\Middleware\FractalNegotiatorMiddleware;
use Concrete\Core\Http\Middleware\OAuthAuthenticationMiddleware;
use Concrete\Core\HTTP\Middleware\OAuthErrorMiddleware;
use Concrete\Core\Http\Middleware\PSR7Middleware;
use Concrete\Core\Routing\RouteCollector;
use Concrete\Core\Routing\RouteProviderInterface;
use Concrete\Core\Routing\Router;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\System\Info;
use Doctrine\ORM\EntityManagerInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\ResourceServer;
use phpseclib\Crypt\RSA;

class APIServiceProvider extends ServiceProvider implements RouteProviderInterface
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
        // Extend the router to include the routes we want
        $this->app->extend(RouterInterface::class, function (RouterInterface $router) {
            $this->registerRoutes($router);
            return $router;
        });

        $this->registerAuthorizationServer();
    }

    /**
     * Provide routes to a router
     *
     * @param \Concrete\Core\Routing\RouterInterface $router
     * @return void
     */
    public function registerRoutes(RouterInterface $router)
    {
        // OAuth routes
        $router->group('/oauth/2.0', function (RouterInterface $router, RouteCollector $collector) {
            // Register middlewares for this grouop
            $collector->addMiddleware(OAuthErrorMiddleware::class);

            // Register routes
            $router->post('/token', [OAuth\Controller::class, 'token']);
        });

        // System routes
        $router->group('/ccm/api/v1/', function (Router $r, RouteCollector $collector) {
            $collector
                ->addMiddleware(OAuthErrorMiddleware::class)
                ->addMiddleware(OAuthAuthenticationMiddleware::class)
                ->addMiddleware(FractalNegotiatorMiddleware::class);

            $r->group('/system', function (Router $r, RouteCollector $collector) {
                //$collector->addScope('system');

                $r->get('/info', [Controller\V1\System::class, 'info']);
                $r->get('/status/queue', [Controller\V1\System::class, 'queueStatus']);
            });

            $r->group('/site', function(Router $r, RouteCollector $collector) {
                //$collector->addScope('site');

                $r->get('/trees', [Controller\V1\Site::class, 'trees']);
            });
        });
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
            $rsa = $this->app->make(RSA::class);

            // Generate a new RSA key
            $keyPair = $rsa->createKey(2048);

            foreach ($keyPair as &$item) {
                $item = str_replace("\r\n", "\n", $item);
            }

            // Save the keypair
            $config->save('api.keypair', $keyPair);
        }

        return $keyPair;
    }

    /**
     * Get a key by handle
     * @param $handle privatekey | publickey
     * @return callable
     */
    private function getKey($handle)
    {
        return function () use ($handle) {
            if (!$this->keyPair) {
                $this->keyPair = $this->getKeyPair();
            }

            return isset($this->keyPair[$handle]) ? $this->keyPair[$handle] : null;
        };
    }

    /**
     * Register the authorization and authentication server classes
     */
    protected function registerAuthorizationServer()
    {
        // The ResourceServer deals with authenticating requests, in other words validating tokens
        $this->app->when(ResourceServer::class)->needs('$publicKey')->give($this->getKey(self::KEY_PUBLIC));

        // AuthorizationServer on the other hand deals with authorizing a session with a username and password and key and secret
        $this->app->when(AuthorizationServer::class)->needs('$privateKey')->give($this->getKey(self::KEY_PRIVATE));
        $this->app->when(AuthorizationServer::class)->needs('$publicKey')->give($this->getKey(self::KEY_PUBLIC));
        $this->app->extend(AuthorizationServer::class, function (AuthorizationServer $server) {
            $server->setEncryptionKey($this->app->make('config/database')->get('concrete.security.token.encryption'));

            $oneHourTTL = new \DateInterval('PT1H');

            // Enable client_credentials grant type with 1 hour ttl
            $server->enableGrantType($this->app->make(ClientCredentialsGrant::class), $oneHourTTL);

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
