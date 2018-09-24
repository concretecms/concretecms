<?php

namespace Concrete\Authentication\ExternalConcrete5;

use Concrete\Core\Authentication\Type\ExternalConcrete5\ServiceFactory;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use League\Url\Url;

class Controller extends GenericOauth2TypeController
{

    /** @var \Concrete\Core\Authentication\Type\ExternalConcrete5\ServiceFactory */
    protected $factory;

    /** @var \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface */
    protected $urlResolver;

    public function __construct(
        \Concrete\Core\Authentication\AuthenticationType $type = null,
        ServiceFactory $factory,
        ResolverManagerInterface $urlResolver)
    {
        parent::__construct($type);
        $this->factory = $factory;
        $this->urlResolver = $urlResolver;
    }

    public function registrationGroupID()
    {
        $config = $this->app->make(Repository::class);
        return $config->get('auth.external_concrete5.registration.group');
    }

    public function supportsRegistration()
    {
        $config = $this->app->make(Repository::class);
        return $config->get('auth.external_concrete5.registration.enabled', false);
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<div class="ccm-concrete-authentication-type-svg" ' .
            'data-src="/concrete/images/authentication/community/concrete.svg">' .
            file_get_contents(DIR_BASE_CORE . '/images/authentication/community/concrete.svg') .
            '</div>';
    }

    public function getHandle()
    {
        return 'external_concrete5';
    }

    /**
     * @return \Concrete\Core\Api\OAuth\Service\ExternalConcrete5
     */
    public function getService()
    {
        if (!$this->service) {
            /** @var \OAuth\ServiceFactory $serviceFactory */
            $serviceFactory = $this->app->make('oauth/factory/service');
            $this->service = $this->factory->createService($serviceFactory);
        }

        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        $passedUrl = $args['url'];
        try {
            $url = Url::createFromUrl($passedUrl);

            if (!(string)$url->getScheme() || !(string)$url->getHost()) {
                throw new \InvalidArgumentException('No scheme or host provided.');
            }

        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid URL.');
        }

        $config = $this->app->make(Repository::class);
        $config->save('auth.external_concrete5.url', $args['url']);
        $config->save('auth.external_concrete5.appid', $args['apikey']);
        $config->save('auth.external_concrete5.secret', $args['apisecret']);
        $config->save('auth.external_concrete5.registration.enabled', (bool)$args['registration_enabled']);
        $config->save('auth.external_concrete5.registration.group', intval($args['registration_group'], 10));
    }

    public function edit()
    {
        $config = $this->app->make(Repository::class);
        $this->set('form', $this->app->make('helper/form'));
        $this->set('data', $config->get('auth.external_concrete5', ''));

        $list = new \GroupList();
        $list->includeAllGroups();
        $this->set('groups', $list->getResults());
    }

    public function form()
    {
        $authUrl = $this->urlResolver->resolve(['/ccm/system/authentication/oauth2/external_concrete5/attempt_auth']);
        $attachUrl = $this->urlResolver->resolve(['/ccm/system/authentication/oauth2/external_concrete5/attempt_attach']);
        $baseUrl = $this->urlResolver->resolve(['/']);
        $path = $baseUrl->getPath();
        $path->remove('index.php');

        $this->set('authUrl', $authUrl);
        $this->set('attachUrl', $attachUrl);
        $this->set('baseUrl', $baseUrl);
        $this->set('assetBase', $baseUrl->setPath($path));
    }
}
