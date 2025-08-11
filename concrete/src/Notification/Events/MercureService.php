<?php

namespace Concrete\Core\Notification\Events;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Notification\Events\ServerEvent\ServerEventInterface;
use Symfony\Component\Mercure\Hub;
use Concrete\Core\Application\Application;

class MercureService implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function getLoggerChannel()
    {
        return Channels::CHANNEL_MESSENGER;
    }

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

    /**
     * Wrapper for getting the hub directly and publishing an update. Useful for ensuring Mercure doesn't
     * raise undue errors.
     *
     * @param ServerEventInterface $event
     */
    public function publish(ServerEventInterface $event)
    {
        $hub = $this->getHub();
        $update = $event->getUpdate();
        try {
            $hub->publish($update);
        } catch (\Exception $e) {
            $this->logger->notice(t('Attempted to send update to Mercure service and failed: %s', $e->getMessage()));
        }
    }

    public function getSubscriber(): Subscriber
    {
        if (!isset($this->subscriber)) {
            $this->subscriber = $this->app->make(Subscriber::class);
        }
        return $this->subscriber;
    }



}

