<?php

namespace Concrete\Core\Authentication\Type\ExternalConcrete;

use Concrete\Core\Api\OAuth\Service\ExternalConcrete;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Url\Url;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Storage\SymfonySession;
use OAuth\ServiceFactory as OAuthServiceFactory;
use Symfony\Component\HttpFoundation\Session\Session;

class ServiceFactory
{

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface
     */
    protected $urlResolver;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;

    /**
     * CommunityServiceFactory constructor.
     * @param \Concrete\Core\Config\Repository\Repository $config
     * @param \Symfony\Component\HttpFoundation\Session\Session $session
     * @param \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $url
     * @param \Concrete\Core\Http\Request $request
     */
    public function __construct(Repository $config, Session $session, ResolverManagerInterface $url, Request $request)
    {
        $this->config = $config;
        $this->session = $session;
        $this->request = $request;
        $this->urlResolver = $url;
    }

    /**
     * Create a service object given a ServiceFactory object
     *
     *
     * @param \OAuth\ServiceFactory $factory
     * @return \OAuth\Common\Service\ServiceInterface
     */
    public function createService(OAuthServiceFactory $factory)
    {
        if ($this->config->has('auth.external_concrete')) {
            $config = $this->config->get('auth.external_concrete');
        } else {
            // legacy support
            $config = $this->config->get('auth.external_concrete5');
        }

        $appId = array_get($config, 'appid');
        $appSecret = array_get($config, 'secret');
        $baseUrl = array_get($config, 'url');

        // Get the callback url
        /** @var Url $callbackUrl */
        $callbackUrl = $this->urlResolver->resolve(['/ccm/system/authentication/oauth2/external_concrete/callback/']);
        if ($callbackUrl->getHost() == '') {
            $callbackUrl = $callbackUrl->setHost($this->request->getHost());
            $callbackUrl = $callbackUrl->setScheme($this->request->getScheme());
        }

        // Create a credential object with our ID, Secret, and callback url
        $credentials = new Credentials($appId, $appSecret, (string) $callbackUrl);

        // Create a new session storage object and pass it the active session
        $storage = new SymfonySession($this->session, false);

        $baseApiUrl = new Uri($baseUrl);

        // Create the service using the oauth service factory
        return $factory->createService('external_concrete', $credentials, $storage, [
            ExternalConcreteService::SCOPE_OPENID,
        ], $baseApiUrl);
    }

}

