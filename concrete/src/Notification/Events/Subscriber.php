<?php

namespace Concrete\Core\Notification\Events;

use Concrete\Core\Application\Application;
use Concrete\Core\Cookie\ResponseCookieJar;
use Concrete\Core\Notification\Events\ServerEvent\ServerEventInterface;
use Concrete\Core\Notification\Events\Topic\TopicInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RS256;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HS256;
use Lcobucci\JWT\Token\Builder as TokenBuilder;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Token\Plain;

class Subscriber
{

    /**
     * Topic URLs to subscribe to in Mercure
     *
     * @var array
     */
    protected $topics = [];

    /**
     * @var ResponseCookieJar
     */
    protected $cookieJar;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, ResponseCookieJar $cookieJar)
    {
        $this->app = $app;
        $this->cookieJar = $cookieJar;
    }

    public function addTopics(array $topics)
    {
        foreach ($topics as $topic) {
            $this->topics[] = (string) $topic;
        }
    }

    /**
     * @param string|TopicInterface $topic
     */
    public function addTopic($topic)
    {
        $this->topics[] = (string) $topic;
    }

    /**
     * @return array
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    public function removeTopics(array $topicsToRemove)
    {
        $newTopics = [];
        foreach ($this->topics as $topic) {
            if (!in_array($topic, $topicsToRemove)) {
                $newTopics[] = $topic;
            }
        }
        $this->topics = $newTopics;
    }

    private function getSubscriberJwt(): string
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
            $keyString = file_get_contents(
                $config->get('concrete.notification.mercure.default.subscriber_private_key_path')
            );
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

        $expires = new \DateTimeImmutable($config->get('concrete.notification.mercure.jwt.subscriber.expires_at'));
        $token = $builder
            ->withClaim('mercure', ['subscribe' => $this->getTopics()])
            ->expiresAt($expires)
            ->getToken(
                $signer,
                $key
            );

        if ($token instanceof Plain) {
            return $token->toString();
        } else {
            return (string)$token;
        }
    }

    public function refreshAuthorizationCookie()
    {
        $config = $this->app->make('config');
        $cookieDomain = $config->get('concrete.notification.mercure.default.cookie_domain');
        return $this->cookieJar->addCookie(
            'mercureAuthorization',
            $this->getSubscriberJwt(),
            0,
            DIR_REL,
            $cookieDomain
        );
    }
}

