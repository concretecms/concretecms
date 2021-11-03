<?php

namespace Concrete\Core\Notification\Events;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Serializer\JsonSerializer;
use Concrete\Core\Notification\Events\ServerEvent\EventInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

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
                $token = (new Builder())
                    ->withClaim('mercure', ['publish' => ['*']])
                    ->getToken(
                        new Sha256(),
                        new Key(
                            $dbConfig->get('concrete.notification.mercure.default.jwt_key')
                        )
                    );

                return (string) $token;
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

