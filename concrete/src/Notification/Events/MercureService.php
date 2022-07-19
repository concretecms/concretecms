<?php

namespace Concrete\Core\Notification\Events;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Notification\Events\ServerEvent\EventInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Lcobucci\JWT\Token\Plain;
use Symfony\Component\Mercure\PublisherInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RS256;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HS256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Lcobucci\JWT\Token\Builder as TokenBuilder;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Signer\Key\InMemory;
/**
 * An object-oriented wrapper for working with Channel objects with the Symfony Mercure service.
 */
class MercureService
{

    /**
     * @var JsonSerializer
     */
    protected $serializer;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var PublisherInterface
     */
    protected $publisher;

    /**
     * @var ResolverManagerInterface
     */
    protected $urlResolver;

    public function __construct(JsonSerializer $serializer, Application $app, Repository $config, ResolverManagerInterface $urlResolver)
    {
        $this->serializer = $serializer;
        $this->app = $app;
        $this->config = $config;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->config->get('concrete.notification.server_sent_events');
    }

    public function getPublisherUrl(): string
    {
        return (string) $this->config->get('concrete.notification.mercure.default.publish_url');
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher(): PublisherInterface
    {
        if (!isset($this->publisher)) {
            $config = $this->config;
            $dbConfig = $this->app->make('config/database');
            $tokenFunction = function () use ($config, $dbConfig) {
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

                $token = $builder
                    ->withClaim('mercure', ['publish' => ['*']])
                    ->getToken(
                        $signer,
                        $key
                    );

                if ($token instanceof Plain) {
                    return $token->toString();
                } else {
                    return (string) $token;
                }

            };

            $this->publisher = new Publisher(
                $this->getPublisherUrl(),
                $tokenFunction
            );
        }
        return $this->publisher;
    }

    public function sendUpdate(EventInterface $event): void
    {
        $publisher = $this->getPublisher();
//        $url = $this->urlResolver->resolve(['/ccm/events', $event->getEvent()]);
        $url = '/ccm/events/' . $event->getEvent();
        $update = new Update((string) $url, $this->serializer->serialize($event, 'json'));
        $publisher($update);
    }
}

