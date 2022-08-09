<?php

namespace Concrete\Core\Notification\Events;

use Concrete\Core\Config\Repository\Repository;
use Symfony\Component\Mercure\Hub;
use Concrete\Core\Application\Application;

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
     * @var Hub
     */
    protected $hub;

    /**
     * @var Subscriber
     */
    protected $subscriber;

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

    public function getHub(): Hub
    {
        if (!isset($this->hub)) {
            $this->hub = $this->app->make(Hub::class);
        }
        return $this->hub;
    }

    public function getSubscriber(): Subscriber
    {
        if (!isset($this->subscriber)) {
            $this->subscriber = $this->app->make(Subscriber::class);
        }
        return $this->subscriber;
    }



}

