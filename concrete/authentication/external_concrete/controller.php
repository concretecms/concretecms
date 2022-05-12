<?php

namespace Concrete\Authentication\ExternalConcrete;

use Concrete\Core\Authentication\Type\ExternalConcrete\ServiceFactory;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Widget\GroupSelector;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\User;
use InvalidArgumentException;
use League\Url\Url;

class Controller extends GenericOauth2TypeController
{
    /**
     * @var \Concrete\Core\Authentication\Type\ExternalConcrete\ServiceFactory
     */
    protected $factory;

    /**
     * @var \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface
     */
    protected $urlResolver;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    public function __construct(
        ?\Concrete\Core\Authentication\AuthenticationType $type,
        ServiceFactory $factory,
        ResolverManagerInterface $urlResolver,
        Repository $config
    ) {
        parent::__construct($type);
        $this->factory = $factory;
        $this->urlResolver = $urlResolver;
        $this->config = $config;
    }

    /**
     * Get the ID of the group to enter upon registration
     * This method grabs the registration group ID out of the auth config group.
     *
     * @return int
     */
    public function registrationGroupID()
    {
        if ($this->config->has('auth.external_concrete.registration.group')) {
            return (int) $this->config->get('auth.external_concrete.registration.group');
        } else {
            // Legacy support
            return (int) $this->config->get('auth.external_concrete5.registration.group');
        }
    }

    /**
     * Determine whether this type supports automatically registering users
     * This method grabs this configuration from the auth config group.
     *
     * @return bool
     */
    public function supportsRegistration()
    {
        if ($this->config->has('auth.external_concrete.registration.enabled')) {
            return (bool) $this->config->get('auth.external_concrete.registration.enabled');
        } else {
            // Legacy support
            return (bool) $this->config->get('auth.external_concrete5.registration.enabled', false);
        }
    }

    /**
     * Build and return this authentication type's icon HTML.
     *
     * @return string
     */
    public function getAuthenticationTypeIconHTML()
    {
        $svgData = file_get_contents(DIR_BASE_CORE . '/images/authentication/community/concrete.svg');
        $publicSrc = '/concrete/images/authentication/community/concrete.svg';

        return "<div class='ccm-concrete-authentication-type-svg' data-src='{$publicSrc}'>{$svgData}</div>";
    }

    public function getHandle()
    {
        return 'external_concrete';
    }

    /**
     * Get the service object associated with this authentication type
     * This method uses the oauth/factory/service object to create our service if one is not set.
     *
     * @return \Concrete\Core\Authentication\Type\ExternalConcrete\ExternalConcreteService
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

    /**
     * Save data for this authentication type
     * This method is called when the type_form.php submits. It stores client details and configuration for connecting.
     *
     * @param array|\Traversable $args
     */
    public function saveAuthenticationType($args)
    {
        $passedUrl = trim($args['url'] ?? '');
        if ($passedUrl !== '') {
            try {
                $url = Url::createFromUrl($passedUrl);

                if (!(string) $url->getScheme() || !(string) $url->getHost()) {
                    throw new InvalidArgumentException('No scheme or host provided.');
                }
            } catch (\Exception $e) {
                throw new InvalidArgumentException('Invalid URL.');
            }
        }

        $passedName = trim($args['displayName'] ?? '');
        if ($passedName === '') {
            throw new InvalidArgumentException('Invalid display name');
        }
        $this->authenticationType->setAuthenticationTypeName($passedName);

        $this->config->save('auth.external_concrete.url', $passedUrl);
        $this->config->save('auth.external_concrete.appid', (string) ($args['apikey'] ?? ''));
        $this->config->save('auth.external_concrete.secret', (string) ($args['apisecret'] ?? ''));
        $this->config->save('auth.external_concrete.registration.enabled', !empty($args['registration_enabled']));
        $this->config->save('auth.external_concrete.registration.group', ((int) ($args['registration_group'] ?? 0)) ?: null);
    }

    /**
     * Controller method for type_form
     * This method is called just before rendering type_form.php, use it to set data for that template.
     */
    public function edit()
    {
        $this->set('groupSelector', $this->app->make(GroupSelector::class));
        $this->set('form', $this->app->make('helper/form'));
        if ($this->config->has('auth.external_concrete')) {
            $this->set('data', (array)$this->config->get('auth.external_concrete', []));
        } else {
            // legacy support
            $this->set('data', (array)$this->config->get('auth.external_concrete5', []));
        }
        $this->set('redirectUri', $this->urlResolver->resolve(['/ccm/system/authentication/oauth2/external_concrete/callback']));

        $list = $this->app->make(GroupList::class);
        $this->set('groups', $list->getResults());
    }

    /**
     * Controller method for form
     * This method is called just before form.php is rendered, use it to set data for that template.
     */
    public function form()
    {
        $this->setData();
    }

    /**
     * Controller method for the hook template
     * This method is called before hook.php is rendered, use it to set data for that template.
     */
    public function hook()
    {
        $this->setData();
    }

    /**
     * Controller method for the hooked template
     * This method gets called before hooked.php is rendered, use it to set data for that template.
     */
    public function hooked()
    {
        $this->setData();
    }

    public function getBindingForUser($user)
    {
        $binding = parent::getBindingForUser($user);
        if ($binding) {
            return $binding;
        } else {
            if (is_object($user)) {
                $userID = $user->getUserID();
            } else {
                $userID = (int) $user;
            }

            return $this->getBindingService()->getUserBinding($userID, 'external_concrete5');
        }
    }

    /**
     * Method for setting general data for all views.
     */
    private function setData()
    {
        if ($this->config->has('auth.external_concrete')) {
            $data = $this->config->get('auth.external_concrete', '');
        } else {
            // legacy support
            $data = $this->config->get('auth.external_concrete5', '');
        }
        $authUrl = $this->urlResolver->resolve(['/ccm/system/authentication/oauth2/external_concrete/attempt_auth']);
        $attachUrl = $this->urlResolver->resolve(['/ccm/system/authentication/oauth2/external_concrete/attempt_attach']);
        $baseUrl = $this->urlResolver->resolve(['/']);
        $path = $baseUrl->getPath();
        $path->remove('index.php');
        $name = trim((string) array_get($data, 'name', t('External concrete')));

        $this->set('data', $data);
        $this->set('authUrl', $authUrl);
        $this->set('attachUrl', $attachUrl);
        $this->set('baseUrl', $baseUrl);
        $this->set('assetBase', $baseUrl->setPath($path));
        $this->set('name', $name);
        $this->set('user', $this->app->make(User::class));
    }
}
