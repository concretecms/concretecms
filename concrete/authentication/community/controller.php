<?php

namespace Concrete\Authentication\Community;

use Concrete\Core\Authentication\Type\Community\Factory\CommunityServiceFactory;
use Concrete\Core\Authentication\Type\Community\Service\Community;
use Concrete\Core\Authentication\Type\Community\Service\Community as CommunityService;
use Concrete\Core\Authentication\Type\OAuth\OAuth2\GenericOauth2TypeController;
use Concrete\Core\Form\Service\Widget\GroupSelector;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\Group\GroupRepository;
use OAuth\ServiceFactory;

/**
 * @deprecated - will be replaced the general External Concrete authentication type in the future.
 */
class Controller extends GenericOauth2TypeController
{
    public function registrationGroupID()
    {
        return \Config::get('auth.community.registration.group');
    }

    public function supportsRegistration()
    {
        return \Config::get('auth.community.registration.enabled', false);
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<div class="ccm-concrete-authentication-type-svg" data-src="/concrete/images/authentication/community/concrete.svg">' .
                    file_get_contents(DIR_BASE_CORE . '/images/authentication/community/concrete.svg') .
               '</div>';
    }

    public function getHandle()
    {
        return 'community';
    }

    /**
     * @return Community
     */
    public function getService()
    {
        if (!$this->service) {
            /** @var ServiceFactory $serviceFactory */
            $serviceFactory = $this->app->make('oauth/factory/service');
            $serviceFactory->registerService('community', CommunityService::class);

            /** @var CommunityServiceFactory $communityFactory */
            $communityFactory = $this->app->make(CommunityServiceFactory::class);
            $this->service = $communityFactory->createService($serviceFactory);
        }

        return $this->service;
    }

    public function saveAuthenticationType($args)
    {
        $config = $this->app->make('config');
        $config->save('auth.community.appid', (string) ($args['apikey'] ?? ''));
        $config->save('auth.community.secret', (string) ($args['apisecret'] ?? ''));
        $config->save('auth.community.registration.enabled', !empty($args['registration_enabled']));
        $config->save('auth.community.registration.group', ((int) ($args['registration_group'] ?? 0)) ?: null);
    }

    public function edit()
    {
        $config = $this->app->make('config');
        $this->set('groupSelector', $this->app->make(GroupSelector::class));
        $this->set('form', $this->app->make('helper/form'));
        $this->set('concreteSecurePrefix', (string) $config->get('concrete.urls.concrete_community'));
        $this->set('callbackURI', $this->app->make(ResolverManagerInterface::class)->resolve(['/ccm/system/authentication/oauth2/community/callback']));
        $this->set('apikey', (string) $config->get('auth.community.appid', ''));
        $this->set('apisecret', (string) $config->get('auth.community.secret', ''));
        $this->set('registrationEnabled', (bool) $config->get('auth.community.registration.enabled'));
        $registrationGroupID = (int) $config->get('auth.community.registration.group');
        $registrationGroup = $registrationGroupID === 0 ? null : $this->app->make(GroupRepository::class)->getGroupById($registrationGroupID);
        $this->set('registrationGroup', $registrationGroup === null ? null : (int) $registrationGroup->getGroupID());
    }

    /**
     * @return array
     */
    public function getAdditionalRequestParameters()
    {
        return ['state' => time()];
    }

    public function getExtractor($new = false)
    {
        try {
            return parent::getExtractor($new);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * Get the URL of the Concrete account associated to a user.
     *
     * @param \Concrete\Core\User\User|\Concrete\Core\User\UserInfo|\Concrete\Core\Entity\User\User|int $user
     *
     * @return string|null returns null if the user is not bound to a Concrete account
     */
    public function getConcreteProfileURL($user)
    {
        $result = null;
        $binding = $this->getBindingForUser($user);
        if ($binding !== null) {
            $concreteUserID = (int) $binding;
            if ($concreteUserID !== 0) {
                $result = "https://community.concretecms.com/profile/-/view/{$concreteUserID}/";
            }
        }

        return $result;
    }
}
