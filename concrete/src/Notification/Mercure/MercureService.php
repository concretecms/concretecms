<?php

namespace Concrete\Core\Notification\Mercure;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Notification\Mercure\Topic\TopicInterface;
use Concrete\Core\Notification\Mercure\Update\UpdateInterface;
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

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
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

    public function sendUpdate(UpdateInterface $update): void
    {
        $publisher = $this->getPublisher();
        $update = new Update($update->getTopicURL(), json_encode($update->getData()));
        $publisher($update);
    }
}

