<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Notification;

use Concrete\Core\Notification\Events\MercureService;
use Concrete\Core\Notification\Events\ServerEvent\TestConnectionEvent;
use Concrete\Core\Notification\Events\Subscriber;
use Concrete\Core\Notification\Events\Topic\TestConnectionTopic;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Utility\Service\Identifier;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\Update;

class Events extends DashboardPageController
{

    public function view()
    {
        $config = $this->app->make("config");
        $dbConfig = $this->app->make('config/database');
        $enable_server_sent_events = (bool) $config->get('concrete.notification.server_sent_events');
        $this->set('enable_server_sent_events', $enable_server_sent_events);
        if ($enable_server_sent_events) {
            $this->set('publishUrl', $config->get('concrete.notification.mercure.default.publish_url'));
            $this->set('cookieDomain', $config->get('concrete.notification.mercure.default.cookie_domain'));

            $connectionMethod = $config->get('concrete.notification.mercure.default.connection_method') ?? null;
            if ($this->request->request->has('connectionMethod')) {
                // handle posts with errors.
                $connectionMethod = $this->request->request->get('connectionMethod');
            }
            
            $jwtKey = $dbConfig->get('concrete.notification.mercure.default.jwt_key') ?? null;
            $publisherPrivateKey = $config->get('concrete.notification.mercure.default.publisher_private_key_path') ?? null;
            $subscriberPrivateKey = $config->get('concrete.notification.mercure.default.subscriber_private_key_path') ?? null;

            if ($this->request->request->has('jwtKey')) {
                $jwtKey = h($jwtKey);
            }
            if ($this->request->request->has('publisherPrivateKey')) {
                $publisherPrivateKey = h($this->request->request->get('publisherPrivateKey'));
            }
            if ($this->request->request->has('subscriberPrivateKey')) {
                $subscriberPrivateKey = h($this->request->request->get('subscriberPrivateKey'));
            }

            $this->set('connectionMethod', $connectionMethod);
            $this->set('jwtKey', $jwtKey);
            $this->set('publisherPrivateKey', $publisherPrivateKey);
            $this->set('subscriberPrivateKey', $subscriberPrivateKey);


            if ($this->isTestConnectionAvailable()) {
                $mercureService = $this->app->make(MercureService::class);
                $subscriber = $mercureService->getSubscriber();
                $subscriber->addTopic(new TestConnectionTopic());
                $subscriber->refreshAuthorizationCookie();

                $this->set('eventSourceUrl', $mercureService->getPublisherUrl());
                $this->set('testConnectionTopicUrl', (new TestConnectionTopic())->getTopicUrl());
                $this->set('isTestConnectionAvailable', true);
            } else {
                $this->set('isTestConnectionAvailable', false);
            }
        }
    }

    /**
     * Looks at a) whether events are enabled b) whether the URL is specified c) whether all the required fields
     * for the connection method are set.
     *
     * @return bool
     */
    protected function isTestConnectionAvailable(): bool
    {
        $config = $this->app->make('config');
        $dbConfig = $this->app->make('config/database');
        $enable_server_sent_events = (bool) $config->get('concrete.notification.server_sent_events');
        if (!$enable_server_sent_events) {
            return false;
        }
        $publishUrl = $config->get('concrete.notification.mercure.default.publish_url') ?? null;
        if (!$publishUrl) {
            return false;
        }
        $connectionMethod = $config->get('concrete.notification.mercure.default.connection_method') ?? null;
        if ($connectionMethod === 'single_secret_key') {
            $jwtKey = $dbConfig->get('concrete.notification.mercure.default.jwt_key') ?? null;
            if ($jwtKey !== null) {
                return true;
            }
        }
        if ($connectionMethod === 'rsa_dual') {
            $publisherPrivateKey  = $config->get('concrete.notification.mercure.default.publisher_private_key_path') ?? null;
            $subscriberPrivateKey  = $config->get('concrete.notification.mercure.default.subscriber_private_key_path') ?? null;
            if ($subscriberPrivateKey !== null && $publisherPrivateKey !== null) {
                return true;
            }
        }
        return false;
    }

    public function enable_server_sent_events()
    {
        if (!$this->token->validate('enable_server_sent_events')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $defaultSite = $this->app->make('site')->getSite();
        if (!$defaultSite->getSiteCanonicalURL()) {
            $this->error->add(t('Your default site must define a canonical URL to enable server-sent-events.'));
        }

        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $config->save('concrete.notification.server_sent_events', true);
            // upon first enabling, let's set this to the single secret key connection method. This is the
            // simple method that most Mercure documentation has wherein it refers to "!changeme!"
            $config->save('concrete.notification.mercure.default.connection_method', 'single_secret_key');
            $this->flash('success', t('Server-Sent Events enabled successfully.'));
            return $this->buildRedirect($this->action('view'));
        } else {
            $this->view();
        }
    }

    public function test_connection()
    {
        $ping = $this->request->request->get('ping');
        $hub = $this->app->make(Hub::class);
        /**
         * @var $subscriber Subscriber
         */
        $event = new TestConnectionEvent($ping);
        $hub->publish($event->getUpdate());

        return new JsonResponse([]); // This is just here for our ajax requests, it has nothing to do with mercure
    }

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }

        $config = $this->app->make('config');
        $dbConfig = $this->app->make('config/database');

        if ($this->request->request->get('disable')) {
            // we clicked the disable button.
            // So let's clear everything out.
            $config->save('concrete.notification.server_sent_events', false);
            $config->save('concrete.notification.mercure.default', null);
            $dbConfig->save('concrete.notification.mercure.default.jwt_key', null);
            $dbConfig->save('concrete.notification.mercure.default.cookie_domain', null);
            $this->flash('success', t('Server-sent events disabled successfully.'));
            return $this->buildRedirect($this->action('view'));
        }

        $connectionMethod = $this->request->request->get('connectionMethod') ?? 'single_secret_key';
        if ($connectionMethod === 'rsa_dual') {
            $subscriberPrivateKey = $this->request->request->get('subscriberPrivateKey');
            $publisherPrivateKey = $this->request->request->get('publisherPrivateKey');
            $filesystem = new Filesystem();
            if (!$subscriberPrivateKey || !$filesystem->exists($subscriberPrivateKey)) {
                $this->error->add(t('You must specify a valid file path for the subscriber private key.'));
            }
            if (!$publisherPrivateKey || !$filesystem->exists($publisherPrivateKey)) {
                $this->error->add(t('You must specify a valid file path for the publisher private key.'));
            }
        }

        if (!$this->error->has()) {

            $config->save('concrete.notification.mercure.default.publish_url',
                  (string) $this->request->request->get('publishUrl')
            );
            $cookieDomain = (string) $this->request->request->get('cookieDomain');
            if ($cookieDomain !== '') {
                $config->save('concrete.notification.mercure.default.cookie_domain', $cookieDomain);
            } else {
                $config->save('concrete.notification.mercure.default.cookie_domain', null);
            }
            $connectionMethod = $this->request->request->get('connectionMethod') ?? 'single_secret_key';
            $config->save('concrete.notification.mercure.default.connection_method', $connectionMethod);
            switch ($connectionMethod) {
                case 'single_secret_key':
                    $dbConfig->save('concrete.notification.mercure.default.jwt_key',
                        (string) $this->request->request->get('jwtKey')
                    );
                    $config->save('concrete.notification.mercure.default.publisher_private_key_path', null);
                    $config->save('concrete.notification.mercure.default.subscriber_private_key_path', null);
                    break;
                case 'rsa_dual':
                    $dbConfig->save('concrete.notification.mercure.default.jwt_key', null);
                    $config->save('concrete.notification.mercure.default.publisher_private_key_path',
                        $publisherPrivateKey
                    );
                    $config->save('concrete.notification.mercure.default.subscriber_private_key_path',
                        $subscriberPrivateKey);
                    break;
            }

            $this->flash('success', t("Settings updated successfully."));
            return $this->buildRedirect(['/dashboard/system/notification/events']);
        }

        $this->view();
    }
}
