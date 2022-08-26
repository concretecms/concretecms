<?php

namespace Concrete\Core\Notification\Events;

use Concrete\Core\Cookie\ResponseCookieJar;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Notification\Events\MercureService;
use Symfony\Component\Mercure\Hub;
use Lcobucci\JWT\Token\Builder as TokenBuilder;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RS256;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HS256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token\Plain;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;


class MercureServiceProvider extends ServiceProvider
{
    private function getPublisherJwt(): string
    {
        $config = $this->app->make('config');
        $dbConfig = $this->app->make('config/database');
        if (class_exists(TokenBuilder::class)) {
            $builder = new TokenBuilder(new JoseEncoder(), ChainedFormatter::default());
        } else {
            $builder = new Builder();
        }
        $connectionMethod = $config->get('concrete.notification.mercure.default.connection_method') ?? null;
        if ($connectionMethod === 'rsa_dual') {
            $keyString = file_get_contents($config->get('concrete.notification.mercure.default.publisher_private_key_path'));
            $signer = new RS256();
        } else {
            $keyString = $dbConfig->get('concrete.notification.mercure.default.jwt_key');
            $signer = new HS256();
        }
        if (class_exists(InMemory::class)) {
            $key = InMemory::plainText($keyString, '');
        } else {
            $key = new Key($keyString, '');
        }

        $expires = new \DateTimeImmutable($config->get('concrete.notification.mercure.jwt.publisher.expires_at'));
        $token = $builder
            ->withClaim('mercure', ['publish' => ['*']])
            ->expiresAt($expires)
            ->getToken(
                $signer,
                $key
            );

        if ($token instanceof Plain) {
            return $token->toString();
        } else {
            return (string) $token;
        }
    }

    public function register()
    {
        $this->app->singleton(MercureService::class);
        $this->app->singleton(Subscriber::class);
        $this->app->singleton(Hub::class, function() {
            $service = $this->app->make(MercureService::class);
            if (!$service->isEnabled()) {
                throw new \RuntimeException(t('You must enable server-sent-events to use this method.'));
            }
            $hub = new Hub($service->getPublisherUrl(), new StaticTokenProvider($this->getPublisherJwt()));
            return $hub;
        });
    }
}
